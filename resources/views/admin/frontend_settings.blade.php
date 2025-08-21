<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>

    <a href="{{ route('toggle.sidebar') }}" class="btn btn-info my-2">
        Switch to Software Settings <i class="fas fa-arrow-right"></i>
    </a>

    <li class="nav-item">
        <a href="{{ route('allservice') }}" class="nav-link {{ Route::is('allservice') ? 'active' : '' }}">
            <i class="nav-icon fas fa-briefcase"></i>
            <p>Services</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ route('products.index') }}" class="nav-link {{ Route::is('products.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-project-diagram"></i>
            <p>Products</p>
        </a>
    </li>

    <li class="nav-item dropdown {{ Route::is('project-types.index') || Route::is('projects.index') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link dropdown-toggle {{ Route::is('project-types.index') || Route::is('projects.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-briefcase"></i>
            <p>
                Portfolio <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('projects.index') }}" class="nav-link {{ Route::is('projects.index') ? 'active' : '' }}">
                    <i class="fas fa-tasks nav-icon"></i>
                    <p>Projects</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('project-types.index') }}" class="nav-link {{ Route::is('project-types.index') ? 'active' : '' }}">
                    <i class="fas fa-layer-group nav-icon"></i>
                    <p>Project Types</p>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item dropdown {{ Route::is('contacts.index') || Route::is('quotations.index') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link dropdown-toggle {{ Route::is('contacts.index') || Route::is('quotations.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-envelope"></i>
            <p>
                Messages <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('contacts.index') }}" class="nav-link {{ Route::is('contacts.index') ? 'active' : '' }}">
                    <i class="fas fa-envelope-open-text nav-icon"></i>
                    <p>Contact Messages</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('quotations.index') }}" class="nav-link {{ Route::is('quotations.index') ? 'active' : '' }}">
                    <i class="fas fa-file-signature nav-icon"></i>
                    <p>Quotations</p>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item dropdown {{ Route::is('admin.companyDetails') || Route::is('admin.aboutUs') || Route::is('admin.privacy-policy') || Route::is('admin.terms-and-conditions') || Route::is('allFaq') || Route::is('allcontactemail') || Route::is('allslider') || Route::is('client-reviews.index') || Route::is('admin.company.seo-meta') || Route::is('team-members.index') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link dropdown-toggle {{ Route::is('admin.companyDetails') || Route::is('admin.aboutUs') || Route::is('admin.privacy-policy') || Route::is('admin.terms-and-conditions') || Route::is('allFaq') || Route::is('allcontactemail') || Route::is('allslider') || Route::is('client-reviews.index') || Route::is('admin.company.seo-meta') || Route::is('team-members.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cog"></i>
            <p>
                Settings <i class="fas fa-angle-left right"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('admin.companyDetails') }}" class="nav-link {{ Route::is('admin.companyDetails') ? 'active' : '' }}">
                    <i class="fas fa-building nav-icon"></i>
                    <p>Company Details</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.aboutUs') }}" class="nav-link {{ Route::is('admin.aboutUs') ? 'active' : '' }}">
                    <i class="fas fa-building nav-icon"></i>
                    <p>About Us</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.privacy-policy') }}" class="nav-link {{ Route::is('admin.privacy-policy') ? 'active' : '' }}">
                    <i class="fas fa-building nav-icon"></i>
                    <p>Privacy Policy</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.terms-and-conditions') }}" class="nav-link {{ Route::is('admin.terms-and-conditions') ? 'active' : '' }}">
                    <i class="fas fa-building nav-icon"></i>
                    <p>Terms & Conditions</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('allFaq') }}" class="nav-link {{ Route::is('allFaq') ? 'active' : '' }}">
                    <i class="fas fa-building nav-icon"></i>
                    <p>FAQ</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.company.seo-meta') }}" class="nav-link {{ Route::is('admin.company.seo-meta') ? 'active' : '' }}">
                    <i class="fas fa-building nav-icon"></i>
                    <p>SEO</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('allcontactemail') }}" class="nav-link {{ Route::is('allcontactemail') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p>Contact Email</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('allslider') }}" class="nav-link {{ Route::is('allslider') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-sliders-h"></i>
                    <p>Slider</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('client-reviews.index') }}" class="nav-link {{ Route::is('client-reviews.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-comments"></i>
                    <p>Client Reviews</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('team-members.index') }}" class="nav-link {{ Route::is('team-members.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>Our Team</p>
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item" style="margin-top: 200px">
    </li>

  </ul>
</nav>