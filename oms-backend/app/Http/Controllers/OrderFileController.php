<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\OrderFile;
use App\Models\Order;
use Log;
class OrderFileController extends Controller
{
    public function index(Order $order)
    {
        $this->authorize('upload', $order);
        return OrderFile::where('order_id', $order->id)
            ->latest()
            ->get();
    }
    public function download(OrderFile $file)
    {
        abort_if(
            !auth()->user()->isAdmin()
            && $file->uploaded_by !== auth()->id()
            && $file->order->customer_id !== auth()->id(),
            403
        );
        $path = storage_path('app/' . $file->file_path);
        return response()->download(
            $path,
            $file->file_name, // 👈 force correct filename
            [
                'Content-Type' => $file->mime_type ?? mime_content_type($path),
            ]
        );
    }

}
