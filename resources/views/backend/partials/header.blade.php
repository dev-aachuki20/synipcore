<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-1">
            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 24 24"
                    class="eva eva-menu-2-outline" fill="#8f9bb3">
                    <g data-name="Layer 2">
                        <g data-name="menu-2">
                            <rect width="24" height="24" transform="rotate(180 12 12)" opacity="0"></rect>
                            <circle cx="4" cy="12" r="1"></circle>
                            <rect x="7" y="11" width="14" height="2" rx=".94" ry=".94"></rect>
                            <rect x="3" y="16" width="18" height="2" rx=".94" ry=".94"></rect>
                            <rect x="3" y="6" width="18" height="2" rx=".94" ry=".94"></rect>
                        </g>
                    </g>
                </svg>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </div>

        <ul class="topbar-menu d-flex align-items-center">
            <li class="dropdown language_switcher">
                <a class="nav-link dropdown-toggle arrow-none lang-btn" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    @if (app()->getLocale() == 'en')
                    <img src="{{ asset('default/flag/eng.png') }}" alt="" class="lang-image">
                    @elseif (app()->getLocale() == 'jp')
                    <img src="{{ asset('default/flag/japanese.png') }}" alt="" class="lang-image">
                    @else
                    <img src="{{ asset('default/flag/chinese.jpg') }}" alt="" class="lang-image">
                    @endif

                    <span class="d-lg-block">
                        <h5 class="my-0 fw-normal">
                            <span class="user-profile-name">{{ app()->getLocale() }}</span>
                        </h5>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown lang-dropdown">
                    @php $languages = App\Models\Language::all(); @endphp
                    @foreach ($languages as $language)
                    <a class="{{ app()->getLocale() === $language->code ? 'active' : '' }}"
                        href="{{ route('language.switch', $language->code) }}">
                        <img src="{{ asset(config('constant.language_flag_image')[$language->code]['image']) }}"
                            alt="{{ $language->name }}" style="">
                        <span>{{ $language->name }}</span></a>
                    @endforeach
                </div>
            </li>
            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none head_notification" data-bs-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false" id="notificationDropdown" data-refresh="true">
                    <i class="ri-notification-3-line fs-22"></i>
                    <span class="noti-icon-badge badge text-bg-pink" id="notification-count">{{auth()->user()->unreadNotifications()->count() ?? 0}}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                    <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 fs-16 fw-semibold"> {{trans('cruds.menus.notifications')}}</h6>
                            </div>
                            <div class="col-auto">
                                <a href="javascript: void(0);" id="mark-all-read" class="text-dark text-decoration-underline">
                                    <small>{{trans('global.mark_all_read')}}</small>
                                </a>
                            </div>
                        </div>
                    </div>


                    <div style="max-height: 300px;overflow-y: auto;" data-simplebar id="notification-items">
                        <!-- Notifications will be loaded dynamically here -->
                    </div>

                    <!-- All-->
                    <a href="{{ route('admin.notifications') }}"
                        class="dropdown-item text-center text-primary text-decoration-underline fw-bold notify-item border-top border-light py-2">
                        {{trans('global.view_all')}}
                    </a>

                </div>
            </li>
            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode">
                    <i class="ri-moon-line fs-22"></i>
                </div>
            </li>
            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        @if (auth()->user()->profile_image_url)
                        <img src="{{ auth()->user()->profile_image_url }}" alt="user-image" width="32"
                            class="rounded-circle user-profile-img">
                        @else
                        <img src="{{ asset(config('constant.default.user_icon')) }}" alt="user-image" width="32"
                            class="rounded-circle">
                        @endif
                    </span>
                    <span class="d-lg-block d-none">
                        <h5 class="my-0 fw-normal">
                            <span class="user-profile-name">{{ ucwords(auth()->user()->name) }}</span> <i
                                class="ri-arrow-down-s-line d-none d-sm-inline-block align-middle"></i>
                        </h5>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">

                    <!-- item-->
                    <a href="{{ route('admin.show.profile') }}" class="dropdown-item">
                        <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                        <span>{{ trans('global.profile') }}</span>
                    </a>

                    <!-- item-->
                    <a href="{{ route('admin.logout') }}" class="dropdown-item">
                        <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                        <span>{{ trans('global.logout') }}</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>