<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li><a href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>
<li><a href='{{ backpack_url('mechanic') }}'><i class='fa fa-users'></i> <span>Mechanics</span></a></li>
{{--<li><a href='{{ backpack_url('driver') }}'><i class='fa fa-tag'></i> <span>Drivers</span></a></li>--}}
<li><a href='{{ backpack_url('garage') }}'><i class='fa fa-map-pin'></i> <span>Garages</span></a></li>
<li><a href='{{ backpack_url('location') }}'><i class='fa fa-map-marker'></i> <span>Locations (dev only)</span></a></li>
<li><a href='{{ backpack_url('request_emergency') }}'><i class='fa fa-bell'></i> <span>Request Emergency</span></a></li>
