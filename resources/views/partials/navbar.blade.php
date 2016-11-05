{{-- Navbar for insertion into primary.blade --}}
<div class="collapse navbar-collapse" id="navbar-collapsible">
    <ul class="nav navbar-nav">
        <li class="dropdown">
          <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Members <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/members" name="menu.member.search">Overview</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/members/new" name="menu.member.new">Add new</a></li>
            {{-- <li><a href="/members/mass" name="menu.member.massactions">Mass actions</a></li> --}}
            {{-- <li role="separator" class="divider"></li> --}}
            {{-- <li><a href="/members/stats" name="menu.member.stats">Statistics</a></li> --}}
            {{-- <li><a href="/members/reports" name="menu.member.reporting">Reporting</a></li> --}}
          </ul>
        </li>
        <li class="dropdown">
          <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Decorations <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/decorations">Overview</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/decorations/new">Create new</a></li>
            <li><a href="#/decorations/gallery">Gallery</a></li>
          </ul>
        </li> 
        {{-- <li class="dropdown">
          <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Activity <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="/activities" name="menu.activity.overview">Overview</a></li>
            <li><a href="/activities/search" name="menu.activity.search">Search</a></li>
            <li role="separator" class="divider"></li>
            <li><a href="/activities/new" name="menu.activity.new">New Activity</a></li>
            <li role="separator" class="divider"></li>
            <!-- <li><a href="/activity/roll" name="menu.activity.roll">Mark Roll</a></li> -->
            <li><a href="/activities/awol" name="menu.activity.awol">All AWOLs</a></li>
          </ul>
        </li> --}}
        <li class="dropdown">
          <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">System Users</a></li>
            <li><a href="#">Audit</a></li>
          </ul>
        </li>
        {{-- <li class="dropdown">
          <a data-target="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Payments <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">System Users</a></li>
            <li><a href="#">Audit</a></li>
          </ul>
        </li> --}}
    </ul>
</div>