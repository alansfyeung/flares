{{-- Search all members --}}
@extends('layouts.template-ng')

@section('ng-app', 'flaresMember')
@section('ng-controller', 'memberIndexController')
@section('title', 'Members')

@section('heading')
<h1>Member Search</h1>
@endsection

@push('scripts')
<script src="{{asset('ng-app/components/member/flaresMemberIndex.js')}}"></script>
@endpush

@section('advancedSearchForm')
<form class="form-horizontal" ng-cloak ng-show="state.isAdvancedSearch" ng-submit="submitAdvancedSearch()">
	<div class="row">
		<div class="col-sm-6">
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
            <div class="form-group" ng-if="formData.ranks">
				<label class="control-label col-sm-3">Rank</label>
				<div class="col-sm-6">
					<select class="form-control" ng-model="searchParams.rank" tabindex="3">
						<option ng-repeat="rank in formData.ranks" value="@{{rank.abbr}}">@{{rank.name}}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Sex</label>
				<div class="col-sm-9">
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="" tabindex="4"/> Any</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="M" tabindex="5"/> Male</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.sex" value="F" tabindex="6"/> Female</label>
				</div>
			</div>
		</div>
		
		<div class="col-sm-6">
			<div class="form-group">
				<label class="control-label col-sm-3">Flares Regt Num</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="search-regt-num" ng-model="searchParams.regt_num" tabindex="7" placeholder="Valid regt num"/>
				</div>
            </div>
            <div class="form-group">
				<label class="control-label col-sm-3">Forums username</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="search-forums-username" ng-model="searchParams.forums_username" tabindex="8" placeholder="Valid username"/>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-sm-3">Discharged</label>
				<div class="col-sm-9">
					<label class="radio-inline"><input type="radio" ng-model="searchParams.discharged" value="" tabindex="9"/> Don't include</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.discharged" value="include" tabindex="10"/> Include</label>
					<label class="radio-inline"><input type="radio" ng-model="searchParams.discharged" value="only" tabindex="11"/> Only</label>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
        <div class="col-sm-6">
            <a class="btn btn-link" ng-click="state.isAdvancedSearch = false;">Simple search</a>
            <!-- <small><a class="btn btn-link" ng-click="state.isAdvancedSearch = true;">Advanced search</a></small> -->
        </div>
		<div class="col-sm-6 text-right">
			<button type="submit" class="btn btn-primary" tabindex="11">Search</button>				
		</div>
	</div>
</form>
@endsection

@section('simpleSearchForm')
<form class="form" ng-cloak ng-show="!state.isAdvancedSearch" ng-submit="submitSimpleSearch()">
    <div class="row">
        <div class="col-sm-12">
            <div class="input-group input-group-lg">
                <input type="text" class="form-control" ng-model="searchKeywords" placeholder="Search members...">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">Go!</button>
                </span>
            </div><!-- /input-group -->
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <a class="btn btn-link" ng-click="state.isAdvancedSearch = true;"><span class="glyphicon glyphicon-export"></span> Advanced search</a>
        </div>
    </div>
</form>
@endsection

@section('content')
    <section class="search-area">
        @yield('simpleSearchForm')
        @yield('advancedSearchForm')
        <hr>
    </section>
    
    <form class="form" ng-submit="submitDefaultSearch()" ng-hide="results.length">
        <div class="row">
            <div class="col-xs-4">
                <div class="input-group">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default" ng-click="submitDefaultSearch()">Retrieve most recent</button>
                    </span>
                    <input type="number" class="form-control" ng-model="retriveNumMostRecent">
                </div>
            </div>
        </div>
    </form>
    
    <section class="search-results">
        <p class="text-muted" ng-show="results.length">@{{results.length}} results for search</p>
        <div class="table-wrapper">
            <table class="table table-hover" ng-show="results.length">
                <colgroup>
                    <col style="width: 120px;">
                    <col style="min-width: 120px;">
                    <col style="min-width: 120px;">
                    <col style="width: 180px;">
                    <col style="width: 80px;">
                    <col style="width: 120px;">
                    <col style="width: 80px;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Regt Num</th>
                        <th>Last Name</th>
                        <th>Given Names</th>
                        <th>Rank</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="result in results" class="search-result-row" ng-class="{'warning': result.deleted_at}" ng-click="selectMember(result)">
                        <td>@{{result.regt_num}}</td>
                        <td>@{{result.last_name}} <span ng-hide="result.is_enrolled" title="This user is inactive. Please complete their details." class="glyphicon glyphicon-exclamation-sign text-danger"></span></td>
                        <td>@{{result.first_name}}</td>
                        <td>@{{result.rank}}</td>
                        <td>@{{result.sex}}</td>
                        <td>@{{result.ageDetails}}</td>
                        <td><a class="btn btn-primary btn-block btn-xs fl-context-modal-button" ng-click="selectMemberContext(result); $event.stopPropagation();">More</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

@endsection
