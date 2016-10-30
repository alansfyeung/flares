{{-- Add a single member using the simple form --}}
@extends('primary')

@section('ng-app', 'flaresMemberNew')
@section('ng-controller', 'newSimpleController')
@section('title', 'Simple Member form')

@push('scripts')
<script src="/app/components/member/flaresMemberNewSimple.js"></script>
@endpush

@section('heading')
<h1>Simple member onboarding form</h1>
@endsection

@section('content')

<div class="alert alert-warning alert-block" ng-if="wf.state.errorMessage">
    @{{wf.state.errorMessage}}
</div>

<section ng-show="wf.state.stage === 1">
    <form class="form-horizontal" ng-submit="wf.submitNewRecord()" name="newSimpleStageOne">
        <h2>Basic details</h2>
        <p>These details are used to match and de-duplicate any existing members. A regimental number is also generated for the new user.</p>
        
        <fieldset>
            <div class="form-group">
                <label class="control-label col-sm-3">Last Name</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text" ng-model="member.data.last_name" placeholder="Last name" ng-disabled="member.isSaved">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Given Names</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text"ng-model="member.data.first_name" placeholder="Given Names" ng-disabled="member.isSaved">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Sex</label>
                <div class="col-sm-3">
                    <select class="form-control" ng-model="member.data.sex" ng-disabled="member.isSaved">
                        <option value="" selected>-- Sex --</option>
                        <option ng-repeat="sex in formData.sexes" value="@{{sex}}">@{{sex}}</option>
                    </select>                    
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">DOB</label>
                <div class="col-sm-3">
                    <input class="form-control" type="date" ng-model="member.data.dob" placeholder="DOB" ng-disabled="member.isSaved">
                </div>
                <div class="col-sm-3">
                    <em>TBA: Calculate Age</em>
                </div>
            </div>
        </fieldset>
        <hr>
        <fieldset>
            <div class="form-group">
                <label class="control-label col-sm-3">Onboarding Type</label>
                <div class="col-sm-9">
                    <select class="form-control" ng-model="ctx.onboardingTypes">
                        <option ng-repeat="obType in formData.onboardingTypes" value="@{{obType.id}}">@{{obType.name}}</option>
                    </select>
                </div>
            </div>
        
            <div class="form-group">
                <label class="control-label col-sm-3">Rank</label>
                <div class="col-sm-9">
                    <select class="form-control" ng-model="ctx.newRank">
                        <option ng-repeat="rank in formData.ranks" value="@{{rank.abbr}}">@{{rank.name}}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Initial Posting</label>
                <div class="col-sm-9">
                    <select class="form-control" ng-model="ctx.newPosting">
                        <option ng-repeat="posting in formData.postings" value="@{{posting.abbr}}">@{{posting.name}}</option>
                    </select> 
                </div>
            </div>
        </fieldset>
        
        <hr>
        <div class="form-group">
            <div class="text-right">
                <button class="btn btn-default" type="button" ng-click="cancel()">Cancel</button>
                <button class="btn btn-primary" type="submit">Create Member</button>
            </div>
        </div>
    </form>
</section>

<section ng-show="wf.state.stage === 2">
    <div class="alert alert-info">
        <strong><span class="glyphicon glyphicon-info-sign"></span> Generated Results:</strong> Review the generated member records. Click Confirm to discard any invalid records, or Back to edit any of the invalid records.
    </div>
    
    <div class="text-right">
        <button type="button" class="btn btn-default" ng-click="workflow.prev()">Back</button>
        <button type="button" class="btn btn-primary" ng-click="workflow.confirmNewRecords()" ng-disabled="!workflow.allNewMembersSaved">Confirm</button>
    </div>

    <form class="form-horizontal" ng-submit="workflow.submitDetailedRecord()" ng-show="workflow.detailedMember.regtNum">
        <h3>Personal particulars</h3>
        <div class="form-group">
            <label class="control-label col-sm-3">Regt Num</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.regtNum" readonly />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Last Name</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.last_name" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">First Name</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.first_name" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Sex</label>
            <div class="col-sm-3">
                <select class="form-control" ng-model="workflow.detailedMember.data.sex"><option ng-repeat="sex in formData.sexes" value="@{{sex}}">@{{sex}}</option></select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Date of Birth</label>
            <div class="col-sm-9">
                <input type="date" class="form-control" ng-model="workflow.detailedMember.data.dob" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">School</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.school" />
            </div>
        </div>
        
        <hr>
        <h3>Contact Details</h3>
        <div class="form-group">
            <label class="control-label col-sm-3">Street Address</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.street_addr" placeholder="e.g. 55/512 Help St"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Suburb</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.suburb" placeholder="e.g. Chatswood"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">State</label>
            <div class="col-sm-3">
                <select class="form-control" ng-model="workflow.detailedMember.data.state">
                    <option ng-repeat="state in ['NSW', 'ACT', 'QLD', 'VIC', 'SA', 'TAS', 'WA', 'NT']" value="@{{state}}">@{{state}}</option>
                </select> 
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Postcode</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.postcode" placeholder="e.g. 2000"/>			
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Member's mobile</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.member_mobile" placeholder="e.g. 0400 123 456"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Member's email address</label>
            <div class="col-sm-9">
                <input type="email" class="form-control" ng-model="workflow.detailedMember.data.member_email" placeholder="e.g. jimmy.russell@highschool.edu.au"/>
            </div>
        </div>
        
        <hr>
        <h3>Parent Details</h3>
        <div class="form-group">
            <label class="control-label col-sm-3">Parent's email</label>
            <div class="col-sm-6">
                <input type="email" class="form-control" ng-model="workflow.detailedMember.data.parent_email" placeholder="e.g. KenRussell@amazingoffice.com.au"/>
            </div>
            <label class="radio-inline col-sm-3"><input type="radio" ng-model="workflow.detailedMember.data.parent_preferred_comm" value="Email"> Preferred?</label>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Parent's mobile</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.parent_mobile" placeholder="e.g. 0400 234 567"/>
            </div>
            <label class="radio-inline col-sm-3"><input type="radio" ng-model="workflow.detailedMember.data.parent_preferred_comm" value="MobilePhone"> Preferred?</label>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Home phone</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.home_phone" placeholder="e.g. 02 9478 9012"/>
            </div>
            <label class="radio-inline col-sm-3"><input type="radio" ng-model="workflow.detailedMember.data.parent_preferred_comm" value="HomePhone"> Preferred?</label>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Parent type</label>
            <div class="col-sm-9">
                <select class="form-control" ng-model="workflow.detailedMember.data.parent_type">
                    <option ng-repeat="parentType in ['Parent', 'Guardian', 'Grandparent', 'Sibling', 'Relative', 'School Administrator', 'Other']" value="@{{parentType}}">@{{parentType}}</option>
                </select> 
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Custodial arrangement</label>
            <div class="col-sm-9">
                <input type="email" class="form-control" ng-model="workflow.detailedMember.data.parent_custodial" placeholder="e.g. Full custody to mother"/>
                <span id="helpBlockParentCustodial" class="help-block">Only add information if there is a special child custody arrangement (such as court-enforced)</span>
            </div>
        </div>

        <hr>
        <h3>Health</h3>
        <div class="form-group">
            <label class="control-label col-sm-3">Allergies</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.med_allergies" placeholder="e.g. Peanuts, bee stings"/>
                <span id="helpBlockMedAllergies" class="help-block">Enter as comma-separated values</span>
            </div>
            <label class="checkbox-inline col-sm-3"><input type="checkbox" ng-model="workflow.detailedMember.data.is_med_lifethreat" ng-true-value="1" ng-false-value="0"> Any life threatening?</label>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Special Dietary Requirements</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.sdr" placeholder="e.g. Vegetarian, No gluten"/>
                <span id="helpBlockSdr" class="help-block">Enter as comma-separated values</span>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Medical conditions</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" ng-model="workflow.detailedMember.data.med_cond" placeholder="e.g. Diabetic"/>
                <span id="helpBlockMedAllergies" class="help-block">Enter as comma-separated values</span>
            </div>
            <label class="checkbox-inline col-sm-3"><input type="checkbox" ng-model="workflow.detailedMember.data.is_med_hmp" ng-true-value="1" ng-false-value="0"> Requires HMP?</label>
        </div>

        <hr>
        <div class="row">
            <div class="col-sm-offset-3 col-sm-9">
                <p>
                    <button type="submit" class="btn btn-primary" ng-disabled="!workflow.detailedMember.regtNum"><span class="glyphicon glyphicon-floppy-disk"></span> Save changes</button>
                </p>
                <p ng-show="workflow.detailedMember.isUpdated" title="Last updated at @{{workflow.detailedMember.lastPersistTime}}"><span class="glyphicon glyphicon-floppy-saved"></span> Saved</p>
                <!-- <button type="button" class="btn btn-default" ng-click="workflow.nextDetailedRecord()">Next member</button> -->
                <p></p>
            </div>
        </div>
    </form>
</section>

<section ng-show="wf.state.stage === 3">
    Complete: Thank you
    
    <a ng-click="">Go and check out this new member</a>
    <a href="reset">Add another member</a>
    
    
</section>
    
    
@endsection
