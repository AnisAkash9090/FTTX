<ul id="sidebarMenu" class="list-unstyled components">

    <!-- Dashboard Panel -->
    <li>
        <a href="{{route('dashboard')  }}">
            <i class="fa fa-dashboard yellow_color"></i>
            <span>Dashboard Panel</span>
        </a>
    </li>

    <!-- OLT Panel -->
 @can('has-permission', '1,2,3,4,10,15')
        <li>
        <a href="#sshLab-Panel" data-toggle="collapse" class="dropdown-toggle ">
           <i class="fa fa-gears green_color"></i>
            <span>Olt Panel</span>
        </a>

        <ul class="collapse list-unstyled {{ request()->routeIs('oltView*','oltAdd*','oltConn*','olt-cli.index*') ? 'show' : '' }}" id="sshLab-Panel">
          @can('has-permission', '1,2,3,4,10')
             <li>
                <a href="{{ route('oltAdd') }}">> Olt Add</a>
            </li>
            @endcan
                 @can('has-permission', '1,15')
            <li>
                <a href="{{ route('oltView') }}" >> Olt View</a>
            </li>
           <li>
    <a href="{{ route('oltConn') }}">> Olt Conn</a>
</li><li>
    <a href="{{ route('olt-cli.index') }}">> Olt CLI</a>
</li>
              @endcan
              
        </ul>
    </li> 
@endcan
        <!-- User Create Panel -->
          @can('has-permission', '1,7,8,9,13,14')
    <li>
        <a href="#Admin-Panel" data-toggle="collapse" class="dropdown-toggle ">
            <i class="fa fa-university blue2_color"></i>
            <span>Admin </span>
        </a>

        <ul class="collapse list-unstyled collapse list-unstyled {{ request()->routeIs('userlist*') ? 'show' : '' }}" id="Admin-Panel">
              @can('has-permission', '1,7,8,9') <li>
                <a href="{{ route('userlist') }}" >> User Add</a>
            </li>@endcan
           @can('has-permission', '1,13,14')
<li>
    <a href="{{ route('roles.index') }}">> Roll Management</a>
</li>@endcan
        </ul>
    </li>
    @endcan

</ul>