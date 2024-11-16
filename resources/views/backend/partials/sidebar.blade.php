<div class="leftside-menu">
    <!-- Topbar Brand Logo -->
    <div class="logo-topbar">
        <a href="/" class="logo logo-light">
            <span class="logo-lg">
                <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}"
                    alt="logo">
            </span>
            <span class="logo-sm">
                <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}"
                    alt="small logo">
            </span>
        </a>
        <a href="/" class="logo logo-dark">
            <span class="logo-lg">
                <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.darklogo')) }}"
                    alt="dark logo">
            </span>
            <span class="logo-sm">
                <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.darklogo')) }}"
                    alt="small logo">
            </span>
        </a>
    </div>
    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!--- Sidemenu -->
        <ul class="side-nav">
            @can('dashboard_access')
            <li class="side-nav-item {{ request()->is('dashboard') ? 'menuitem-active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"
                    class="side-nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="ri-pulse-line"></i>
                    <span> @lang('cruds.menus.dashboard') </span>
                </a>
            </li>
            @endcan

            @can('user_access')
            <li class="side-nav-item {{ request()->is('users*') || request()->is('admin/users/*/edit') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/users/*/edit') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesUser" aria-expanded="false"
                    aria-controls="sidebarPagesUser">
                    <i class=" ri-user-line"></i>
                    <span> {{ trans('cruds.menus.user') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/users*') ? 'show' : '' }}" id="sidebarPagesUser">
                    <ul class="side-nav-second-level">
                        @can('user_create')
                        <li>
                            <a class="{{ request()->is('admin/users/create') ? 'active' : '' }}"
                                href="{{ route('admin.users.create') }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('user_access')
                        <li>
                            <a class="{{ request()->is('admin/users*') && !request()->is('admin/users/create') ? 'active' : '' }}"
                                href="{{ route('admin.users.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan

                    </ul>
                </div>
            </li>
            @endcan

            @can('location_access')
            <li class="side-nav-item {{ request()->is('locations*') || request()->is('admin/locations/*/edit') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/locations/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesLocations" aria-expanded="false"
                    aria-controls="sidebarPagesLocations">
                    <i class="ri-map-pin-line"></i>
                    <span> @lang('cruds.menus.locations') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/locations*') ? 'show' : '' }}" id="sidebarPagesLocations">
                    <ul class="side-nav-second-level">
                        @can('location_create')
                        <li>
                            <a href="{{ route('admin.locations.create') }}"
                                class="{{ request()->is('admin.locations.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('location_access')
                        <li>
                            <a href="{{ route('admin.locations.index') }}"
                                class="{{ request()->is('admin/locations*') && !request()->is('admin/locations/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('provider_access')
            <li class="side-nav-item {{ request()->is('providers*') || request()->is('admin/providers/*/edit') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/providers/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesProviders" aria-expanded="false"
                    aria-controls="sidebarPagesProviders">
                    <i class="ri-snowy-line"></i>
                    <span> @lang('cruds.menus.providers') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/providers*') ? 'show' : '' }}"
                    id="sidebarPagesProviders">
                    <ul class="side-nav-second-level">
                        @can('provider_create')
                        <li>
                            <a href="{{ route('admin.providers.create') }}"
                                class="{{ request()->is('admin.providers.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('provider_access')
                        <li>
                            <a href="{{ route('admin.providers.index') }}"
                                class="{{ request()->is('admin/providers*') && !request()->is('admin/providers/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('service_category_access')
            <li class="side-nav-item {{ request()->is('service-categories*') || request()->is('admin/service-categories/*/edit') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/service-categories/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesServicesCategory" aria-expanded="false"
                    aria-controls="sidebarPagesServicesCategory">
                    <i class="ri-quote-text"></i>
                    <span> @lang('cruds.menus.service_categories') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/service-categories*') ? 'show' : '' }}" id="sidebarPagesServicesCategory">
                    <ul class="side-nav-second-level">
                        @can('service_category_create')
                        <li>
                            <a href="{{ route('admin.service-categories.create') }}"
                                class="{{ request()->is('admin.service-categories.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('service_category_access')
                        <li>
                            <a href="{{ route('admin.service-categories.index') }}"
                                class="{{ request()->is('admin/service-categories*') && !request()->is('admin/service-categories/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('service_access')
            <li class="side-nav-item {{ request()->is('services*') || request()->is('admin/services/*/edit') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/services/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesServices" aria-expanded="false"
                    aria-controls="sidebarPagesServices">
                    <i class="ri-shield-check-line"></i>
                    <span> @lang('cruds.menus.services') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/services*') ? 'show' : '' }}" id="sidebarPagesServices">
                    <ul class="side-nav-second-level">
                        @can('service_create')
                        <li>
                            <a href="{{ route('admin.services.create') }}"
                                class="{{ request()->is('admin.services.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('service_access')
                        <li>
                            <a href="{{ route('admin.services.index') }}"
                                class="{{ request()->is('admin/services*') && !request()->is('admin/services/create')  ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('service_booking_access')
            <li class="side-nav-item {{ request()->is('service-bookings*') || request()->is('admin/service-bookings/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/service-bookings/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesServiceBookings" aria-expanded="false"
                    aria-controls="sidebarPagesServiceBookings">
                    <i class="ri-calendar-todo-line"></i>
                    <span> @lang('cruds.menus.service_bookings') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/service-bookings*') ? 'show' : '' }}"
                    id="sidebarPagesServiceBookings">
                    <ul class="side-nav-second-level">
                        @can('service_booking_access')
                        <li>
                            <a href="{{ route('admin.service-bookings.index') }}"
                                class="{{ request()->is('admin.service-bookings.index') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('society_access')
            <li class="side-nav-item {{ request()->is('societies*')  || request()->is('admin/societies/*/edit') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/societies/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesSocieties" aria-expanded="false"
                    aria-controls="sidebarPagesSocieties">
                    <i class="ri-group-2-line"></i>
                    <span> @lang('cruds.menus.societies') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/societies*') ? 'show' : '' }}" id="sidebarPagesSocieties">
                    <ul class="side-nav-second-level">
                        @can('society_create')
                        <li>
                            <a href="{{ route('admin.societies.create') }}"
                                class="{{ request()->is('admin.societies.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('society_access')
                        <li>
                            <a href="{{ route('admin.societies.index') }}"
                                class="{{ request()->is('admin/societies*') && !request()->is('admin/societies/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('building_access')
            <li class="side-nav-item {{ request()->is('buildings*') || request()->is('admin/buildings/*/edit') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/buildings/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesBuildings" aria-expanded="false"
                    aria-controls="sidebarPagesBuildings">
                    <i class="ri-building-line"></i>
                    <span> @lang('cruds.menus.buildings') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/buildings*') ? 'show' : '' }}"
                    id="sidebarPagesBuildings">
                    <ul class="side-nav-second-level">

                        @can('building_create')
                        <li>
                            <a href="{{ route('admin.buildings.create') }}"
                                class="{{ request()->is('admin.buildings.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('building_access')
                        <li>
                            <a href="{{ route('admin.buildings.index') }}"
                                class="{{ request()->is('admin/buildings*') && !request()->is('admin/buildings/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('unit_access')
            <li class="side-nav-item {{ request()->is('units*') || request()->is('admin/units/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/units/*/edit') ? 'active' : ''}} " data-bs-toggle="collapse"
                    href="#sidebarPagesUnits" aria-expanded="false" aria-controls="sidebarPagesUnits">
                    <i class="ri-ruler-line"></i>
                    <span> @lang('cruds.menus.units') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/units*') ? 'show' : '' }}" id="sidebarPagesUnits">
                    <ul class="side-nav-second-level">
                        @can('unit_create')
                        <li>
                            <a href="{{ route('admin.units.create') }}"
                                class="{{ request()->is('admin.units.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('unit_access')
                        <li>
                            <a href="{{ route('admin.units.index') }}"
                                class="{{ request()->is('admin/units*') && !request()->is('admin/units/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('announcement_access')
            <li class="side-nav-item {{ request()->is('announcements*') || request()->is('admin/announcements/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/announcements/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesAnnouncements" aria-expanded="false"
                    aria-controls="sidebarPagesAnnouncements">
                    <i class="ri-megaphone-line"></i>
                    <span> @lang('cruds.menus.notice_board') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/announcements*') ? 'show' : '' }}"
                    id="sidebarPagesAnnouncements">
                    <ul class="side-nav-second-level">
                        @can('announcement_create')
                        <li>
                            <a href="{{ route('admin.announcements.create') }}"
                                class="{{ request()->is('admin.announcements.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('announcement_access')
                        <li>
                            <a href="{{ route('admin.announcements.index') }}"
                                class="{{ request()->is('admin/announcements*') && !request()->is('admin/announcements/create')  ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('complaint_access')
            <li class="side-nav-item {{ request()->is('complaints*') || request()->is('admin/complaints/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/complaints/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesComplaints" aria-expanded="false"
                    aria-controls="sidebarPagesComplaints">
                    <i class="ri-feedback-line"></i>
                    <span> @lang('cruds.menus.complaints') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/complaints*') ? 'show' : '' }}"
                    id="sidebarPagesComplaints">
                    <ul class="side-nav-second-level">
                        @can('complaint_access')
                        <li>
                            <a href="{{ route('admin.complaints.index') }}"
                                class="{{ request()->is('admin/complaints*') && !request()->is('admin/complaints/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('complaint_type_access')
            <li class="side-nav-item {{ request()->is('complaint-types*') || request()->is('admin/complaint-types/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/complaint-types/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesComplaintTypes" aria-expanded="false"
                    aria-controls="sidebarPagesComplaintTypes">
                    <i class="ri-questionnaire-line"></i>
                    <span> @lang('cruds.menus.complaints_types') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/complaint-types*') ? 'show' : '' }}"
                    id="sidebarPagesComplaintTypes">
                    <ul class="side-nav-second-level">
                        @can('complaint_type_create')
                        <li>
                            <a href="{{ route('admin.complaint-types.create') }}"
                                class="{{ request()->is('admin.complaint-types.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('complaint_type_access')
                        <li>
                            <a href="{{ route('admin.complaint-types.index') }}"
                                class="{{ request()->is('admin/complaint-types*') && !request()->is('admin/complaint-types/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('resident_access')
            <li class="side-nav-item {{ request()->is('residents*') || request()->is('admin/residents/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/residents/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesResidents" aria-expanded="false"
                    aria-controls="sidebarPagesResidents">
                    <i class="ri-home-smile-line"></i>
                    <span> @lang('cruds.menus.residents') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/residents*') ? 'show' : '' }}"
                    id="sidebarPagesResidents">
                    <ul class="side-nav-second-level">
                        @can('resident_create')
                        <li>
                            <a href="{{ route('admin.residents.create') }}"
                                class="{{ request()->is('admin.residents.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('resident_access')
                        <li>
                            <a href="{{ route('admin.residents.index') }}"
                                class="{{ request()->is('admin/residents*') && !request()->is('admin/residents/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('guard_access')
            <li class="side-nav-item {{ request()->is('guards*') || request()->is('admin/guards/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/guards/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesGuards" aria-expanded="false"
                    aria-controls="sidebarPagesGuards">
                    <i class="ri-user-6-line"></i>
                    <span> @lang('cruds.menus.guards') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/guards*') ? 'show' : '' }}" id="sidebarPagesGuards">
                    <ul class="side-nav-second-level">
                        @can('guard_create')
                        <li>
                            <a href="{{ route('admin.guards.create') }}"
                                class="{{ request()->is('admin.guards.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('guard_access')
                        <li>
                            <a href="{{ route('admin.guards.index') }}"
                                class="{{ request()->is('admin/guards*') && !request()->is('admin/guards/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('visitor_access')
            <li class="side-nav-item {{ request()->is('visitors*') || request()->is('admin/visitors/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/visitors/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesVisitors" aria-expanded="false"
                    aria-controls="sidebarPagesVisitors">
                    <i class="ri-account-pin-box-line"></i>
                    <span> @lang('cruds.menus.visitor_logs') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/visitors*') ? 'show' : '' }}"
                    id="sidebarPagesVisitors">
                    <ul class="side-nav-second-level">
                        @can('visitor_create')
                        <li>
                            <a href="{{ route('admin.visitors.create') }}"
                                class="{{ request()->is('admin/visitors/create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('visitor_access')
                        <li>
                            <a href="{{ route('admin.visitors.index') }}"
                                class="{{ request()->is('admin/visitors*') && !request()->is('admin/visitors/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('resident_vehicle_access')
            <li class="side-nav-item {{ request()->is('resident-vehicles*') || request()->is('admin/resident-vehicles/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/resident-vehicles/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesResidentVehicles" aria-expanded="false"
                    aria-controls="sidebarPagesResidentVehicles">
                    <i class="ri-car-washing-line"></i>
                    <span> @lang('cruds.menus.resident_vehicles') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/resident-vehicles*') ? 'show' : '' }}"
                    id="sidebarPagesResidentVehicles">
                    <ul class="side-nav-second-level">
                        @can('resident_vehicle_create')
                        <li>
                            <a href="{{ route('admin.resident-vehicles.create') }}"
                                class="{{ request()->is('admin.resident-vehicles.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('resident_vehicle_access')
                        <li>
                            <a href="{{ route('admin.resident-vehicles.index') }}"
                                class="{{ request()->is('admin.resident-vehicles.index') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                        @can('resident_pre_approved_vehicle_access')
                        <li>
                            <a href="{{ route('admin.preApprovedVehicle') }}"
                                class="{{ request()->is('admin/resident-vehicles*') && !request()->is('admin/resident-vehicles/create') ? 'active' : '' }}">{{ trans('global.resident_pre_approved_vehicle_list') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('resident_daily_help_access')
            <li class="side-nav-item {{ request()->is('resident-daily-helps*') || request()->is('admin/resident-daily-helps/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/resident-daily-helps/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesResidentDailyHelp" aria-expanded="false"
                    aria-controls="sidebarPagesResidentDailyHelp">
                    <i class="ri-folder-info-line"></i>
                    <span> @lang('cruds.menus.resident_daily_helps') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/resident-daily-helps*') ? 'show' : '' }}"
                    id="sidebarPagesResidentDailyHelp">
                    <ul class="side-nav-second-level">
                        @can('resident_daily_help_create')
                        <li>
                            <a href="{{ route('admin.resident-daily-helps.create') }}"
                                class="{{ request()->is('admin.resident-daily-helps.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('resident_daily_help_access')
                        <li>
                            <a href="{{ route('admin.resident-daily-helps.index') }}"
                                class="{{ request()->is('admin/resident-daily-helps*') && !request()->is('admin/resident-daily-helps/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('amenity_access')
            <li class="side-nav-item {{ request()->is('amenities*') || request()->is('admin/amenities/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/amenities/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesAmenity" aria-expanded="false"
                    aria-controls="sidebarPagesAmenity">
                    <i class="ri-robot-line"></i>
                    <span> @lang('cruds.menus.amenities') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/amenities*') ? 'show' : '' }}"
                    id="sidebarPagesAmenity">
                    <ul class="side-nav-second-level">
                        @can('amenity_create')
                        <li>
                            <a href="{{ route('admin.amenities.create') }}"
                                class="{{ request()->is('admin.amenities.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('amenity_access')
                        <li>
                            <a href="{{ route('admin.amenities.index') }}"
                                class="{{ request()->is('admin/amenities*') && !request()->is('admin/amenities/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('comment_access')
            <li class="side-nav-item {{ request()->is('comments*') || request()->is('admin/comments/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/comments/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesComments" aria-expanded="false"
                    aria-controls="sidebarPagesComments">
                    <i class="ri-message-2-line"></i>
                    <span> @lang('cruds.menus.comments') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/comments*') ? 'show' : '' }}"
                    id="sidebarPagesComments">
                    <ul class="side-nav-second-level">
                        @can('comment_access')
                        <li>
                            <a href="{{ route('admin.comments.index') }}"
                                class="{{ request()->is('admin/comments*') && !request()->is('admin/comments/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('post_access')
            <li class="side-nav-item {{ request()->is('posts*') || request()->is('admin/amenities/*/posts')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/posts/*/edit') || request()->is('admin/posts/comment-detail/*') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesPosts" aria-expanded="false"
                    aria-controls="sidebarPagesPosts">
                    <i class="ri-flag-line"></i>
                    <span> @lang('cruds.menus.posts') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/posts*') ? 'show' : '' }}" id="sidebarPagesPosts">
                    <ul class="side-nav-second-level">
                        @can('post_create')
                        <li>
                            <a href="{{ route('admin.posts.create') }}"
                                class="{{ request()->is('admin.posts.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('post_access')
                        <li>
                            <a href="{{ route('admin.posts.index') }}"
                                class="{{  request()->is('admin/posts*') && !request()->is('admin/posts/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('amenity_booking_access')
            <li class="side-nav-item {{ request()->is('amenity/bookings*') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{ request()->is('admin/amenity/bookings') ? 'active' : '' }}"
                    data-bs-toggle="collapse" href="#sidebarPagesBookings" aria-expanded="false"
                    aria-controls="sidebarPagesBookings">
                    <i class="ri-calendar-todo-line"></i>
                    <span> @lang('cruds.menus.amenity_booking') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/amenity/bookings*') ? 'show' : '' }}"
                    id="sidebarPagesBookings">
                    <ul class="side-nav-second-level">
                        @can('amenity_booking_access')
                        <li>
                            <a href="{{ route('admin.amenity.booking.index') }}"
                                class="{{ request()->is('admin/amenity/bookings*') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('transaction_access')
            <li class="side-nav-item {{ request()->is('transactions*') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/transactions/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesTransaction" aria-expanded="false"
                    aria-controls="sidebarPagesTransaction">
                    <i class="ri-sound-module-line"></i>
                    <span> @lang('cruds.menus.transaction') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/transactions*') ? 'show' : '' }}"
                    id="sidebarPagesTransaction">
                    <ul class="side-nav-second-level">
                        @can('transaction_report_access')
                        <li>
                            <a href="{{ route('admin.transaction-reports') }}"
                                class="{{ request()->is('admin.transaction-reports.index') ? 'active' : '' }}">{{ trans('cruds.menus.transaction_reports') }}</a>
                        </li>
                        @endcan
                        @can('transaction_access')
                        <li>
                            <a href="{{ route('admin.transactions.index') }}"
                                class="{{ request()->is('admin.transactions.index') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('payment_request_access')
            <li class="side-nav-item {{ request()->is('payment-requests*') || request()->is('admin/payment-requests/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/payment-requests/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesPaymentRequest" aria-expanded="false"
                    aria-controls="sidebarPagesPaymentRequest">
                    <i class="ri-wallet-3-line"></i>
                    <span> @lang('cruds.menus.payment_requests') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/payment-requests*') ? 'show' : '' }}"
                    id="sidebarPagesPaymentRequest">
                    <ul class="side-nav-second-level">
                        @can('payment_request_create')
                        <li>
                            <a href="{{ route('admin.payment-requests.create') }}"
                                class="{{ request()->is('admin.payment-requests.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('payment_request_access')
                        <li>
                            <a href="{{ route('admin.payment-requests.index') }}"
                                class="{{ request()->is('admin/payment-requests*') && !request()->is('admin/payment-requests/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('payment_method_access')
            <li class="side-nav-item {{ request()->is('payment-methods*') || request()->is('admin/payment-methods/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/payment-methods/*/edit') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesPaymentMethod" aria-expanded="false"
                    aria-controls="sidebarPagesPaymentMethod">
                    <i class="ri-currency-line"></i>
                    <span> {{ trans('cruds.menus.payment_methods') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/payment-methods*') ? 'show' : '' }}"
                    id="sidebarPagesPaymentMethod">
                    <ul class="side-nav-second-level">
                        @can('payment_method_create')
                        <li>
                            <a class="{{ request()->is('admin/payment-methods/create') ? 'active' : '' }}"
                                href="{{ route('admin.payment-methods.create') }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('payment_method_access')
                        <li>
                            <a class="{{ request()->is('admin/payment-methods*') && !request()->is('admin/payment-methods/create') ? 'active' : '' }}"
                                href="{{ route('admin.payment-methods.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('faq_access')
            <li class="side-nav-item {{ request()->is('faqs*') || request()->is('admin/faqs/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/faqs/*/edit') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesFaq" aria-expanded="false"
                    aria-controls="sidebarPagesFaq">
                    <i class="ri-question-line"></i>
                    <span> {{ trans('cruds.menus.faqs') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/faqs*') ? 'show' : '' }}" id="sidebarPagesFaq">
                    <ul class="side-nav-second-level">
                        @can('faq_create')
                        <li>
                            <a class="{{ request()->is('admin/faqs/create') ? 'active' : '' }}"
                                href="{{ route('admin.faqs.create') }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('faq_access')
                        <li>
                            <a class="{{ request()->is('admin/faqs*') && !request()->is('admin/faqs/create') ? 'active' : '' }}"
                                href="{{ route('admin.faqs.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('property_type_access')
            <li class="side-nav-item {{ request()->is('prpoertyTypes*') || request()->is('admin/prpoertyTypes/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/prpoertyTypes/*/edit') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesPropertyType" aria-expanded="false"
                    aria-controls="sidebarPagesPropertyType">
                    <i class="ri-home-office-line"></i>
                    <span> {{ trans('cruds.menus.property_types') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/prpoertyTypes*') ? 'show' : '' }}"
                    id="sidebarPagesPropertyType">
                    <ul class="side-nav-second-level">
                        @can('property_type_create')
                        <li>
                            <a class="{{ request()->is('admin/prpoertyTypes/create') ? 'active' : '' }}"
                                href="{{ route('admin.prpoertyTypes.create') }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('property_type_access')
                        <li>
                            <a class="{{ request()->is('admin/prpoertyTypes*') && !request()->is('admin/prpoertyTypes/create') ? 'active' : '' }}"
                                href="{{ route('admin.prpoertyTypes.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('property_management_access')
            <li class="side-nav-item {{ request()->is('property-managements*') || request()->is('admin/property-managements/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{ request()->is('admin/property-managements') || request()->is('admin/property-managements/create') || request()->is('admin/property-managements/*/edit') ||  request()->is('admin/property-managements/reports') ? 'active' : '' }}"
                    data-bs-toggle="collapse" href="#sidebarPagesPropertyManagement" aria-expanded="false"
                    aria-controls="sidebarPagesPropertyManagement">
                    <i class="ri-home-gear-line"></i>
                    <span> @lang('cruds.menus.property_managements') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/property-managements*') ? 'show' : '' }}"
                    id="sidebarPagesPropertyManagement">
                    <ul class="side-nav-second-level">
                        @can('payment_request_create')
                        <li>
                            <a href="{{ route('admin.property-managements.create') }}"
                                class="{{ request()->is('admin.property-managements.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('property_management_access')
                        <li>
                            <a href="{{ route('admin.property-managements.index') }}"
                                class="{{ request()->is('admin/property-managements*') && !request()->is('admin/property-managements/create') && !request()->is('admin/property-managements-reports') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan

                        @can('property_management_report')
                        <li>
                            <a href="{{ route('admin.reports') }}"
                                class="{{ request()->is('admin/property-managements-reports') && !request()->is('admin/property-managements') ? 'active' : '' }}">{{ trans('global.report') }}</a>
                        </li>
                        @endcan

                    </ul>
                </div>
            </li>
            @endcan

            @can('category_access')
            <li class="side-nav-item {{ request()->is('categories*') || request()->is('admin/categories/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/categories/*/edit') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesCategory" aria-expanded="false"
                    aria-controls="sidebarPagesCategory">
                    <i class="ri-dashboard-line"></i>
                    <span> {{ trans('cruds.menus.maintenance_categories') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/categories*') ? 'show' : '' }}"
                    id="sidebarPagesCategory">
                    <ul class="side-nav-second-level">
                        @can('category_create')
                        <li>
                            <a class="{{ request()->is('admin/categories/create') ? 'active' : '' }}"
                                href="{{ route('admin.categories.create') }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('category_access')
                        <li>
                            <a class="{{ request()->is('admin/categories*') && !request()->is('admin/categories/create') ? 'active' : '' }}"
                                href="{{ route('admin.categories.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('maintenance_item_access')
            <li class="side-nav-item {{ request()->is('maintenance-items*') || request()->is('admin/maintenance-items/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/maintenance-items/*/edit') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesmaintenanceItem" aria-expanded="false"
                    aria-controls="sidebarPagesmaintenanceItem">
                    <i class="ri-folder-shield-2-line"></i>
                    <span> {{ trans('cruds.menus.maintenance_items') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/maintenance-items*') ? 'show' : '' }}"
                    id="sidebarPagesmaintenanceItem">
                    <ul class="side-nav-second-level">
                        @can('maintenance_item_create')
                        <li>
                            <a class="{{ request()->is('admin/maintenance-items/create') ? 'active' : '' }}"
                                href="{{ route('admin.maintenance-items.create') }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('maintenance_item_access')
                        <li>
                            <a class="{{ request()->is('admin/maintenance-items*') && !request()->is('admin/maintenance-items/create') ? 'active' : '' }}"
                                href="{{ route('admin.maintenance-items.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('maintenance_plan_access')
            <li class="side-nav-item {{ request()->is('maintenance-plans*') || request()->is('admin/maintenance-plans/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/maintenance-plans/*/edit') || request()->is('admin/maintenance-plans/*') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesmaintenanceIPlan" aria-expanded="false"
                    aria-controls="sidebarPagesmaintenanceIPlan">
                    <i class="ri-lightbulb-flash-line"></i>
                    <span> {{ trans('cruds.menus.maintenance_plans') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/maintenance-plans*') ? 'show' : '' }}"
                    id="sidebarPagesmaintenanceIPlan">
                    <ul class="side-nav-second-level">
                        @can('maintenance_plan_create')
                        <li>
                            <a class="{{ request()->is('admin/maintenance-plans/create') ? 'active' : '' }}"
                                href="{{ route('admin.maintenance-plans.create') }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('maintenance_plan_access')
                        <li>
                            <a class="{{ request()->is('admin/maintenance-plans*') && !request()->is('admin/maintenance-plans/create') ? 'active' : '' }}"
                                href="{{ route('admin.maintenance-plans.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('delivery_type_access')
            <li class="side-nav-item {{ request()->is('delivery-types*') || request()->is('admin/delivery-types/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/delivery-types/*/edit') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesDeliveryType" aria-expanded="false"
                    aria-controls="sidebarPagesDeliveryType">
                    <i class="ri-truck-line"></i>
                    <span> {{ trans('cruds.menus.delivery_types') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/delivery-types*') ? 'show' : '' }}"
                    id="sidebarPagesDeliveryType">
                    <ul class="side-nav-second-level">
                        @can('delivery_type_create')
                        <li>
                            <a class="{{ request()->is('admin/delivery-types/create') ? 'active' : '' }}"
                                href="{{ route('admin.delivery-types.create') }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('delivery_type_access')
                        <li>
                            <a class="{{ request()->is('admin/delivery-types*') && !request()->is('admin/delivery-types/create') ? 'active' : '' }}"
                                href="{{ route('admin.delivery-types.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('delivery_management_access')
            <li class="side-nav-item {{ request()->is('delivery-managments*') || request()->is('admin/delivery-managments/*/edit')  ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/delivery-managments/*/edit') || request()->is('delivery-managments') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesDeliveryManagement" aria-expanded="false"
                    aria-controls="sidebarPagesDeliveryManagement">
                    <i class="ri-file-settings-line"></i>
                    <span> {{ trans('cruds.menus.delivery_managements') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/delivery-managements*') ? 'show' : '' }}"
                    id="sidebarPagesDeliveryManagement">
                    <ul class="side-nav-second-level">
                        @can('delivery_management_create')
                        <li>
                            <a class="{{ request()->is('admin/delivery-managements/create') ? 'active' : '' }}"
                                href="{{ route('admin.delivery-managements.create') }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('delivery_management_access')
                        <li>
                            <a class="{{ request()->is('admin/delivery-managements*') && !request()->is('admin/delivery-managements/create') ? 'active' : '' }}"
                                href="{{ route('admin.delivery-managements.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('camera_access')
            <li class="side-nav-item {{ request()->is('cameras*') || request()->is('admin/cameras/*/edit') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/cameras/*/edit') ? 'active' : ''}} "
                    data-bs-toggle="collapse" href="#sidebarPagesCameras" aria-expanded="false"
                    aria-controls="sidebarPagesCameras">
                    <i class="ri-camera-fill"></i>
                    <span> @lang('cruds.menus.cameras') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/cameras*') ? 'show' : '' }}"
                    id="sidebarPagesCameras">
                    <ul class="side-nav-second-level">
                        @can('camera_create')
                        <li>
                            <a href="{{ route('admin.cameras.create') }}"
                                class="{{ request()->is('admin.cameras.create') ? 'active' : '' }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('camera_access')
                        <li>
                            <a href="{{ route('admin.cameras.index') }}"
                                class="{{ request()->is('admin/cameras*') && !request()->is('admin/cameras/create') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('role_access')
            <li class="side-nav-item {{ request()->is('roles*') || request()->is('admin/roles/*/edit') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/roles/*/edit') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesRole" aria-expanded="false"
                    aria-controls="sidebarPagesRole">
                    <i class="ri-user-settings-line"></i>
                    <span> {{ trans('cruds.menus.roles') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/roles*') ? 'show' : '' }}" id="sidebarPagesRole">
                    <ul class="side-nav-second-level">
                        @can('role_create')
                        <li>
                            <a class="{{ request()->is('admin/roles/create') ? 'active' : '' }}"
                                href="{{ route('admin.roles.create') }}">{{ trans('global.add_new') }}</a>
                        </li>
                        @endcan
                        @can('role_access')
                        <li>
                            <a class="{{ request()->is('admin/roles*') && !request()->is('admin/roles/create') ? 'active' : '' }}"
                                href="{{ route('admin.roles.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('permission_access')
            <li class="side-nav-item {{ request()->is('permissions*') || request()->is('admin/permissions/*/edit') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{request()->is('admin/permissions/*/edit') ? 'active' : ''}} " data-bs-toggle="collapse" href="#sidebarPagesPermission" aria-expanded="false"
                    aria-controls="sidebarPagesPermission">
                    <i class="ri-shield-keyhole-line"></i>
                    <span> {{ trans('cruds.menus.permissions') }} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/permissions*') ? 'show' : '' }}"
                    id="sidebarPagesPermission">
                    <ul class="side-nav-second-level">
                        @can('permission_access')
                        <li>
                            <a class="{{ request()->is('admin/permissions') ? 'active' : '' }}"
                                href="{{ route('admin.permissions.index') }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('admin_message_access')
            <li class="side-nav-item {{ request()->is('messages*') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{ request()->is('admin/messages') ? 'active' : '' }}"
                    data-bs-toggle="collapse" href="#sidebarPagesMesage" aria-expanded="false"
                    aria-controls="sidebarPagesMesage">
                    <i class="ri-mail-open-line"></i>
                    <span> {{trans('cruds.menus.message')}} </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/messages*') ? 'show' : '' }}"
                    id="sidebarPagesMesage">
                    <ul class="side-nav-second-level">
                        @can('admin_message_access')
                        <li>
                            <a href="{{ route('admin.messages.index') }}"
                                class="{{ request()->is('admin.messages.index') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('support_access')
            <li class="side-nav-item {{ request()->is('supports*') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{ request()->is('admin/supports') ? 'active' : '' }}"
                    data-bs-toggle="collapse" href="#sidebarPagesSupports" aria-expanded="false"
                    aria-controls="sidebarPagesSupports">
                    <i class="ri-24-hours-line"></i>
                    <span> @lang('cruds.menus.supports') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/supports*') ? 'show' : '' }}"
                    id="sidebarPagesSupports">
                    <ul class="side-nav-second-level">
                        @can('support_access')
                        <li>
                            <a href="{{ route('admin.supports.index') }}"
                                class="{{ request()->is('admin.supports.index') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('notification_access')
            <li class="side-nav-item {{ request()->is('notifications') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{ request()->is('admin/notifications') ? 'active' : '' }}"
                    data-bs-toggle="collapse" href="#sidebarPagesNotification" aria-expanded="false"
                    aria-controls="sidebarPagesNotification">
                    <i class="ri-notification-3-line"></i>
                    <span> @lang('cruds.menus.notifications') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/notifications') ? 'show' : '' }}"
                    id="sidebarPagesNotification">
                    <ul class="side-nav-second-level">
                        @can('notification_access')
                        <li>
                            <a href="{{ route('admin.notifications') }}"
                                class="{{ request()->is('admin.notifications') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('aibox_notification_access')
            <li class="side-nav-item {{ request()->is('ai-box-notifications') ? 'menuitem-active' : '' }}">
                <a class="side-nav-link {{ request()->is('admin/ai-box-notifications') ? 'active' : '' }}"
                    data-bs-toggle="collapse" href="#sidebarPagesAiBoxNotification" aria-expanded="false"
                    aria-controls="sidebarPagesAiBoxNotification">
                    <i class="ri-notification-badge-line"></i>
                    <span> @lang('cruds.menus.aibox_notifications') </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ request()->is('admin/ai-box-notifications') ? 'show' : '' }}"
                    id="sidebarPagesAiBoxNotification">
                    <ul class="side-nav-second-level">
                        @can('aibox_notification_access')
                        <li>
                            <a href="{{ route('admin.aibox.index') }}"
                                class="{{ request()->is('admin.aibox.index') ? 'active' : '' }}">{{ trans('global.view_all') }}</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcan

            @can('setting_access')
            <li class="side-nav-item {{ request()->is('settings') ? 'menuitem-active' : '' }}">
                <a href="{{ route('admin.show.setting') }}"
                    class="side-nav-link {{ request()->is('admin/settings') ? 'active' : '' }}">
                    <i class="ri-settings-3-line"></i>
                    <span> @lang('cruds.menus.setting') </span>
                </a>
            </li>
            @endcan
        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>