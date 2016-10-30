<div ng-show="member.regt_num && state.isDischarge()" ng-cloak>
	<div class="row">
		<form class="form-horizontal col-xs-12">
			<h3>Discharge member</h3>
			
			<div class="form-group">
				<label class="control-label col-sm-3">Discharge Date</label>
				<div class="col-sm-9">
					<input type="date" class="form-control" ng-model="dischargeContext.effectiveDate"/>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-sm-3">Discharge with different rank</label>
				<div class="col-sm-9">
					<div class="checkbox">
						<label><input type="checkbox" ng-model="dischargeContext.isCustomRank" aria-label="Discharge with different rank"> Tick to select a different terminating rank</label>
					</div>
				</div>
			</div>
			
			<div class="form-group" ng-show="dischargeContext.isCustomRank">
				<label class="control-label col-sm-3">Terminating rank</label>
				<div class="col-sm-9">
					<select class="form-control" ng-model="dischargeContext.dischargeRank">
						<option ng-repeat="rank in formData.ranks" value="@{{rank.abbr}}">@{{rank.name}}</option>
					</select>
				</div>
			</div>
			
			<div class="alert alert-info" ng-show="state.isAsync">
				<span class="glyphicon glyphicon-info-sign"></span> Working on your request.
			</div>
			
			<div class="form-group">
				<div class="col-sm-9 col-sm-push-3">
					<button type="button" class="btn btn-warning" ng-click="discharge()" ng-disabled="state.isAsync">Continue with Discharge</button>
					<button type="button" class="btn btn-default" ng-click="cancelDischarge()" ng-disabled="state.isAsync">Cancel</button>
				</div>
			</div>
		</form>
	</div>
</div>