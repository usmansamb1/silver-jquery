<!-- Vehicle Management Menu -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <i class="fas fa-car"></i>
        </span>
        <span class="nav-link-title">My Vehicles</span>
    </a>
</li>

<!-- RFID Management Menu -->
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('rfid.*') ? 'active' : '' }}" href="{{ route('rfid.index') }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <i class="fas fa-id-card"></i>
        </span>
        <span class="nav-link-title">RFID Management</span>
    </a>
</li> 