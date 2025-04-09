<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            @php
                use App\Models\ContactTypes;
                $contactTypes = ContactTypes::whereHas('contacts')->withCount('contacts')->get();
            @endphp
            <ul>
                <li class="menu-title"><span>Main</span></li>

                <li>
                    <a href="{{ route('admin.dashboard.page') }}"
                        class="{{ request()->routeIs('admin.dashboard.page') ? 'active' : '' }}">
                        <i class="fa fa-home"></i> <span>Dashboard</span>
                    </a>
                </li>

                {{-- Contact --}}
                @if (hasPermission('contact'))
                    <li
                        class="submenu {{ request()->routeIs('contact.*') || request()->routeIs('contact-type.*') || request()->routeIs('contact-group.*') ? 'active' : '' }}">
                        <a href="#"><i class="fa fa-user"></i> <span>Contact</span> <span
                                class="menu-arrow"></span></a>
                        <ul style="display: none;">
                            {{-- All Contacts --}}
                            <li>
                                <a href="{{ route('contact.index') }}"
                                    class="{{ request()->routeIs('contact.index') ? 'active' : '' }}">
                                    All Contacts
                                </a>
                            </li>
                            @foreach ($contactTypes as $contactType)
                                <li>
                                    <a href="{{ route('contact.index', ['type' => $contactType->name]) }}">
                                        {{ $contactType->name }}
                                        <span class="badge bg-primary">{{ $contactType->contacts_count }}</span>
                                    </a>
                                </li>
                            @endforeach


                            {{-- Extra sub-permissions --}}
                            @php
                                $contactSubPermissions = [
                                    'contact-type' => ['label' => 'Contact Type', 'route' => 'contact-type.index'],
                                    'contact-group' => ['label' => 'Contact Group', 'route' => 'contact-group.index'],
                                    // Add more contact-related submenus here as needed
                                ];
                            @endphp

                            @foreach ($contactSubPermissions as $key => $item)
                                @if (hasPermission($key))
                                    <li>
                                        <a href="{{ route($item['route']) }}"
                                            class="{{ request()->routeIs($key . '.*') ? 'active' : '' }}">
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @endif

                @if (hasPermission('contacts-export'))
                    <li class="menu-title"><span>Export Option</span></li>
                    <li class="submenu">
                        <a href="#"><i class="fa fa-user-shield"></i> <span>Export</span> <span
                                class="menu-arrow"></span></a>
                        <ul style="display: none;">
                            <li>
                                <a href="{{ route('contacts.export.all') }}"
                                    class="{{ request()->routeIs('admin-user.index') ? 'active' : '' }}">
                                    Export All Contacts
                                </a>
                            </li>
                            @foreach ($contactTypes as $contactType)
                                <li>
                                    <a href="{{ route('contacts.export.type', $contactType->id) }}">
                                        Export {{ $contactType->name }} Contacts
                                        <span class="badge bg-primary">{{ $contactType->contacts_count }}</span>
                                    </a>
                                </li>
                            @endforeach


                        </ul>
                    </li>
                @endif

                {{-- Admin Option --}}
                @if (hasPermission('admin-user') || hasPermission('role') || hasPermission('permission'))
                    <li class="menu-title"><span>Admin Option</span></li>
                    <li
                        class="submenu {{ request()->is('admin-user*') || request()->is('role*') || request()->is('permission*') ? 'active' : '' }}">
                        <a href="#"><i class="fa fa-user-shield"></i> <span>Admin User</span> <span
                                class="menu-arrow"></span></a>
                        <ul style="display: none;">
                            @if (hasPermission('admin-user'))
                                <li>
                                    <a href="{{ route('admin-user.index') }}"
                                        class="{{ request()->routeIs('admin-user.index') ? 'active' : '' }}">
                                        Users
                                    </a>
                                </li>
                            @endif
                            @if (hasPermission('role'))
                                <li>
                                    <a href="{{ route('role.index') }}"
                                        class="{{ request()->routeIs('role.index') ? 'active' : '' }}">
                                        Role
                                    </a>
                                </li>
                            @endif
                            @if (hasPermission('permission'))
                                <li>
                                    <a href="{{ route('permission.index') }}"
                                        class="{{ request()->routeIs('permission.index') ? 'active' : '' }}">
                                        Permission
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{-- Theme Option --}}
                @if (hasPermission('theme-option'))
                    <li>
                        <a href="{{ route('theme-option.index') }}"
                            class="{{ request()->routeIs('theme-option.index') ? 'active' : '' }}">
                            <i class="fa fa-tasks"></i> <span>Theme Option</span>
                        </a>
                    </li>
                @endif

                {{-- Setting --}}
                @if (hasPermission('setting'))
                    <li>
                        <a href="{{ route('setting.index') }}"
                            class="{{ request()->routeIs('setting.index') ? 'active' : '' }}">
                            <i class="fa fa-cog"></i> <span>Setting</span>
                        </a>
                    </li>
                @endif

            </ul>

        </div>
    </div>
</div>
