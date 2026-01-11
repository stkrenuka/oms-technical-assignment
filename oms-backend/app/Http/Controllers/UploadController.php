<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Upload;
use App\Models\UploadChunk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Models\OrderFile;
use Log;


class UploadController extends Controller
{

  public function init(Request $request)
{
    $request->validate([
        'order_id'     => ['required', 'exists:orders,id'],
        'file_name'    => ['required', 'string', 'max:255'],
        'total_chunks' => ['required', 'integer', 'min:1'],
    ]);

    $order = Order::findOrFail($request->order_id);
    $this->authorize('upload', $order);

    $uploadId = Str::uuid();

    // ✅ Store RELATIVE path (best practice)
    $relativePath = "orders/{$order->id}/{$uploadId}_{$request->file_name}";

    Upload::create([
        'upload_id'    => $uploadId,
        'order_id'     => $order->id,
        'file_path'    => $relativePath, // ✅ relative path
        'total_chunks' => $request->total_chunks,
        'created_by'   => auth()->id(),
        'status'       => 'pending',
        'file_name'   => $request->file_name
    ]);

    return response()->json([
        'upload_id' => $uploadId,
    ]);
}


    public function uploadChunk(Request $request)
    {
        $request->validate([
            'upload_id'   => ['required', 'uuid'],
            'chunk_index' => ['required', 'integer', 'min:0'],
            'chunk'       => ['required', 'file'],
        ]);

        $upload = Upload::where('upload_id', $request->upload_id)->firstOrFail();
        $order  = Order::findOrFail($upload->order_id);

        $this->authorize('upload', $order);

        $path = storage_path("app/chunks/{$upload->upload_id}");
        File::ensureDirectoryExists($path);

        $request->file('chunk')->move($path, $request->chunk_index);

        UploadChunk::updateOrCreate(
            [
                'upload_id'   => $upload->upload_id,
                'chunk_index' => $request->chunk_index,
            ],
            ['is_uploaded' => true]
        );

        Upload::where('upload_id', $upload->upload_id)
            ->increment('uploaded_chunks');

        return response()->json(['success' => true]);
    }


    public function status(string $uploadId)
    {
        $upload = Upload::where('upload_id', $uploadId)->firstOrFail();
        $order  = Order::findOrFail($upload->order_id);

        // 🔐 Policy authorization
        $this->authorize('upload', $order);

        $chunks = UploadChunk::where('upload_id', $uploadId)
            ->where('is_uploaded', true)
            ->pluck('chunk_index');

        return response()->json($chunks);
    }


  public function complete(string $uploadId)
{
    $upload = Upload::where('upload_id', $uploadId)->firstOrFail();
    $order  = Order::findOrFail($upload->order_id);

    $this->authorize('upload', $order);

    // Absolute paths
    $finalPath = storage_path("app/{$upload->file_path}");
    $directory = dirname($finalPath);

    // ✅ Ensure directory exists
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    $chunkPath = storage_path("app/chunks/{$upload->upload_id}");

    $final = fopen($finalPath, 'ab');

    for ($i = 0; $i < $upload->total_chunks; $i++) {
        $chunkFile = "$chunkPath/$i";

        if (!file_exists($chunkFile)) {
            fclose($final);
            throw new \Exception("Missing chunk {$i}");
        }

        fwrite($final, file_get_contents($chunkFile));
    }

    fclose($final);

    // Cleanup
    File::deleteDirectory($chunkPath);

    // Detect real MIME type AFTER file exists
    $mime = mime_content_type($finalPath);

    $upload->update([
        'mime_type' => $mime,
        'status'    => 'completed',
    ]);
     OrderFile::create([
        'order_id'    => $order->id,
        'file_name'   => basename($upload->file_path),
        'file_path'   => $upload->file_path,
        'mime_type'   => $mime,
        'uploaded_by' => auth()->id(),
    ]);

    return response()->json(['message' => 'Upload completed']);
}

}
