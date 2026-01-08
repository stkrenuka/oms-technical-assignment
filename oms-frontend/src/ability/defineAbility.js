import { AbilityBuilder, createMongoAbility } from '@casl/ability'

export function defineAbilityFor(user) {
  const { can, cannot, build } = new AbilityBuilder(createMongoAbility)

  if (!user || !user.role) {
    return build()
  }

  if (user.role === 'admin') {
    can('manage', 'all')
  }

  if (user.role === 'customer') {
    can('read', 'Order')
    can('create', 'Order')
    cannot('delete', 'Order')
  }

  return build()
}
