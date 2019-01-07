{{-- Navbar for insertion into primary.blade --}}
<div class="collapse navbar-collapse" id="navbar-collapsible">
    <ul class="nav navbar-nav">
        <li class="dropdown">
          <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Members <span class="caret"></span></a>
          <ul class="dropdown-menu">
            @if (Auth::user()->hasAccessLevel('create'))
            <li><a href="{{url('/members/new')}}" name="menu.member.new">Add new</a></li>
            @endif
            <!-- <li role="separator" class="divider"></li> -->
            <li><a href="{{url('/members')}}" name="menu.member.search">Advanced search</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Decorations <span class="caret"></span></a>
          <ul class="dropdown-menu">
            @if (Auth::user()->hasAccessLevel('create'))
            <li><a href="{{ route('decoration::new') }}">Create new</a></li>
            <li><a href="{{ route('decoration::index') }}">Manage collection</a></li>
            <li role="separator" class="divider"></li>
            @endif
            <li><a href="{{ route('public::decorationList') }}">Public gallery  &nbsp;<span class="glyphicon glyphicon-share text-muted"></span></a></li>
          </ul>
        </li> 
        {{-- <li class="dropdown">
          <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Activity <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{{url('/activities')}}" name="menu.activity.overview">Overview</a></li>
            <li><a href="{{url('/activities/search')}}" name="menu.activity.search">Search</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="{{url('/activities/new')}}" name="menu.activity.new">New Activity</a></li>
            <li role="separator" class="divider"></li>
            <!-- <li><a href="/activity/roll" name="menu.activity.roll">Mark Roll</a></li> -->
            <li><a href="{{url('/activities/awol')}}" name="menu.activity.awol">All AWOLs</a></li>
          </ul>
        </li> --}}
        @if (Auth::user()->hasAccessLevel('admin'))
        <li class="dropdown">
          <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{{url('/users')}}">Admin Users</a></li>
            <li><a href="{{url('/users/patokens')}}">PA Tokens</a></li>
          </ul>
        </li>
        @endif
    </ul>
    @if (Auth::check())
    <ul class="nav navbar-nav navbar-right">
        <li title="{{Auth::user()->email}}">
            <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> {{Auth::user()->username}} <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li>
                    {{-- https://github.com/laravel/framework/blob/7d116dc5a008e69c97f864af79ac46ab6a8d5895/src/Illuminate/Auth/Console/stubs/make/views/layouts/app.stub#L62 --}}
                    <a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
                </li>
            </ul>
        </li>
    </ul>
    @endif
</div>