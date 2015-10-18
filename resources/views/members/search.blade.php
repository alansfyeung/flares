{{-- Search all members --}}
@extends('master')

@section('ng-app', 'flaresMemberSearch')
@section('ng-controller', 'memberSearchController')
@section('title', 'Members')

@section('heading')
<div class="page-header container-fluid">
	<h1>Search all members</h1>
</div>
@endsection

@section('memberSearch')
<form class="form-horizontal" ng-submit="submitSearch()">
	<div class="row">
	
		<div class="col-sm-6 col-md-4">
			<div class="form-group">
				<label class="control-label col-sm-3">Surname</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="search-surname" ng-model="searchParams.last_name" tabindex="1" placeholder="Any last names"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Given Names</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="search-given-names" ng-model="searchParams.first_name" tabindex="2" placeholder="Any given names"/>
				</div>
			</div>			
			<div class="form-group">
				<label class="control-label col-sm-3">Sex</label>
				<div class="col-sm-9">
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="" tabindex="5"/> Any</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="M" tabindex="6"/> Male</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="F" tabindex="7"/> Female</label>
				</div>
			</div>
		</div>
		
		<div class="col-sm-6 col-md-4">
			<div class="form-group">
				<label class="control-label col-sm-3">Rank</label>
				<div class="col-sm-9">
					<select class="form-control" ng-model="searchParams.rank" tabindex="3">
						<option ng-repeat="rank in formData.ranks" value="@{{rank.abbr}}">@{{rank.name}}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Regt Num</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="search-regt-num" ng-model="searchParams.regt_num" tabindex="4" placeholder="Any regt number"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Discharged</label>
				<div class="col-sm-9">
					<label class="radio-inline"><input type="radio" ng-model="searchParams.discharged" value="" tabindex="8"/> Don't include</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.discharged" value="include" tabindex="9"/> Include</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.discharged" value="only" tabindex="10"/> Only</label>
				</div>
			</div>
		</div>
		
		<div class="col-sm-6 col-md-4">
			<button type="submit" class="btn btn-primary" tabindex="11">Search</button>
		</div>
		
	</div>
</form>

<hr/>

<section class="search-results">
	<div class="label label-default">@{{results.length}} results for search</div>
	<table class="table table-hover" ng-show="results.length > 0">
		<thead>
			<tr>
				<th>Regt Num</th>
				<th>Last Name</th>
				<th>Given Names</th>
				<th>Rank</th>
				<th>Sex</th>
				<th>Age</th>
			</tr>
		</thead>
		<tbody>
			<tr ng-repeat="result in results" ng-class="{'danger': !result.is_active, 'warning': result.deleted_at}"  ng-click="selectMember(result)">
				<td>@{{result.regt_num}}</td>
				<td>@{{result.last_name}}</td>
				<td>@{{result.first_name}}</td>
				<td>@{{result.rank}}</td>
				<td>@{{result.sex}}</td>
				<td>@{{result.ageDetails}}</td>
				<!-- <td><a href="/member#!/@{{result.regt_num}}/view">View Member</a></td> -->
			</tr>
		</tbody>
	</table>
</section>
@endsection

@section('memberSearchModal')
{{-- This blade template section is no longer used --}}
<div class="modal" id="memberSearchContextMenu" tabindex="-1" role="dialog" aria-labelledby="activeMemberTitle">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="activeMemberTitle">
					<span>@{{activeMember.last_name}}, @{{activeMember.first_name}}</span>
				</h4>
				<h5 class="modal-subtitle">@{{activeMember.regt_num}}</h5>
			</div>
			<div class="modal-body text-center">
				<a href="/member#!/@{{activeMember.regt_num}}/view" id="viewActiveMember" class="btn btn-block btn-primary activemember-view">View/Edit member details</a>
				<button class="btn btn-block btn-default">Find roll entries [TBA]</button>
			</div>
			<div class="modal-footer">
				<button class="btn btn-block btn-danger" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
@endsection


@section('content')
	@yield('memberSearch')
@endsection


@section('ng-script')
<script src="/app/components/member/flaresMemberSearch.js"></script>
@endsection
