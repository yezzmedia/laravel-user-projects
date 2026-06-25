<div class="space-y-6">
    <x-user-projects::page-header
        :title="__('user-projects::ui.permissions_title')"
        :subtitle="__('user-projects::ui.permissions_description')"
        color="indigo"
    >
        <x-slot:icon><x-user-projects::icon name="shield-check" class="h-5 w-5" /></x-slot:icon>
        <x-slot:actions>
            @if (!$this->editingRoleId)
                <button type="button" wire:click="$toggle('showCreateForm')"
                    class="inline-flex items-center gap-1.5 border border-indigo-300 bg-white px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                    <x-user-projects::icon name="plus" class="h-3.5 w-3.5" />
                    Create Role
                </button>
            @endif
        </x-slot:actions>
    </x-user-projects::page-header>

    @if ($showCreateForm ?? false)
        <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-fuchsia-50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 dark:from-indigo-950/30 dark:via-purple-950/30 dark:to-fuchsia-950/30">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">New Role</h2>
            </div>
            <div class="p-4 sm:p-6">
                <form wire:submit="createRole" class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="newRoleName" class="block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Role Key</label>
                            <input wire:model="newRoleName" id="newRoleName" type="text" required placeholder="e.g. editor"
                                class="mt-1 block w-full border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @error('newRoleName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="newRoleLabel" class="block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Display Label</label>
                            <input wire:model="newRoleLabel" id="newRoleLabel" type="text" required placeholder="Editor"
                                class="mt-1 block w-full border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @error('newRoleLabel') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Permissions</p>
                        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach (($pageData['allPermissions'] ?? []) as $perm)
                                <label class="flex items-center gap-2 border border-gray-200 bg-white px-3 py-2 text-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                                    <input type="checkbox" wire:model="newRolePermissions" value="{{ $perm }}"
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600">
                                    <span class="text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', ucfirst($perm)) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 border border-indigo-300 bg-white px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                            Create Role
                        </button>
                        <button type="button" wire:click="$set('showCreateForm', false)"
                            class="inline-flex items-center gap-1.5 border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        @foreach (($pageData['roles'] ?? collect()) as $role)
            <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900 @if($role->is_system) opacity-90 @endif">
                <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-fuchsia-50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 dark:from-indigo-950/30 dark:via-purple-950/30 dark:to-fuchsia-950/30">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $role->label }}</h2>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="startEditing({{ $role->id }})"
                                class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                                <x-user-projects::icon name="pencil" class="h-4 w-4" />
                            </button>
                            @if (!$role->is_system)
                                <button type="button" wire:click="deleteRole({{ $role->id }})" wire:confirm="Are you sure you want to delete this role?"
                                    class="rounded-lg p-1.5 text-gray-400 hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-950 dark:hover:text-rose-400">
                                    <x-user-projects::icon name="trash" class="h-4 w-4" />
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    @if ($this->editingRoleId === $role->id)
                        <form wire:submit="updateRole" class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Display Label</label>
                                <input wire:model="editRoleLabel" type="text" required
                                    class="mt-1 block w-full border border-gray-200 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Permissions</p>
                                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach (($pageData['allPermissions'] ?? []) as $perm)
                                        <label class="flex items-center gap-2 border border-gray-200 bg-white px-3 py-2 text-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
                                            <input type="checkbox" wire:model="editRolePermissions" value="{{ $perm }}"
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600">
                                            <span class="text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', ucfirst($perm)) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex gap-3 pt-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 border border-indigo-300 bg-white px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-50 dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/20">
                                    Save Changes
                                </button>
                                <button type="button" wire:click="cancelEditing"
                                    class="inline-flex items-center gap-1.5 border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 font-mono mb-1">{{ $role->name }}</p>
                                @if ($role->is_system)
                                    <x-user-projects::badge class="bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">System role</x-user-projects::badge>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3">
                            @if (($role->permissions ?? []) !== [])
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($role->permissions as $perm)
                                        <x-user-projects::badge class="bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                            {{ str_replace('_', ' ', $perm) }}
                                        </x-user-projects::badge>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400 dark:text-gray-500">No permissions assigned.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
