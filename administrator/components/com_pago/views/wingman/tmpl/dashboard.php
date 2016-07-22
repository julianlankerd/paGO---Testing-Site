<div class="pg-col-12">
	<h2 class="pg-mb-20">Hello {{ $root.subscriber.metadata.name }}, welcome back</h2>
	<div id="tabs">
		<div class="pg-tabs">
			<ul>
				<li><a href="#tab-1">Dashboard</a></li>
				<li><a href="#tab-2">Keywords and Competitors</a></li>
				<li><a href="#tab-3">My Account</a></li>
				<!--<li><a href="#tab-4">Need help?</a></li>-->
			</ul>
			<div class="clear"></div>
		</div>
		<div class="tabs-content">
			
			<div id="tab-1" ng-controller="S3Controller" ng-init="load()">
				<div class="pg-row pg-mt-20">
					<div class="pg-col-6">
						<h4>Your SEO Specialist</h4>
						<div class="pg-pad-20 pg-white-bckg pg-border">
							<div class="pg-row">
								<div class="pg-col-6">
									<div class="pg-text">
										<span class="field-heading">
											<label>Name</label>
										</span>
										<div>
											{{ analyst.analyst_name }}
										</div>
									</div>
								</div>
								<div class="pg-col-6">
									<div class="pg-text">
										<span class="field-heading">
											<label>Status</label>
										</span>
										<div>
											{{ status[ analyst.status ] }}
										</div>
									</div>
								</div>
							</div>
							
							<!--
							<div class="pg-mt-20">
								<a href="javascript:void(0);">Please create a Username and Password that is Super Admin for your SEO Specialist.</a> <a href="" class="hasTooltip" title="For your SEO Specialist to provide maximum results we will need access to your site. to make adjustments as needed. You will be informed along the way all aspects of changes so you can watch your sales grow.">Why?</a>
								<form class="pg-mt-20" ng-submit="updateSubscription()">
									<div class="pg-row">
										<div class="pg-col-5">
											<div class="pg-text">
												<span class="field-heading">
													<label>Username</label>
												</span>
											</div>
											<input type="text" ng-model="subscription.analyst_login">
										</div>
										<div class="pg-col-5">
											<div class="pg-text">
												<span class="field-heading">
													<label>Password</label>
												</span>
											</div>
											<input type="password" ng-model="subscription.analyst_password">
										</div>
										<div class="pg-col-2">
											<div class="pg-text">
												<span class="field-heading">
													<label>&nbsp;</label>
												</span>
											</div>
											<button class="pg-btn pg-btn-large pg-btn-green" type="submit" ng-disabled="loading">{{ loading ? '...' : 'Save' }}</button>
										</div>
									</div>
								</form>
							</div>
							-->
							
							<div class="pg-text pg-mt-20">
								<span class="field-heading">
									<label>Message log</label>
								</span>
								<div class="pg-border pg-pad-20 messages">
									
									<div ng-if="loading">Loading...</div>
									<div ng-if="!messages.length && !loading">No messages yet.</div>
									<div ng-repeat="msg in messages" ng-class="{'me': msg.name == $root.subscriber.metadata.name}">
										<span class="author" ng-if="msg.name != $root.subscriber.metadata.name">{{ msg.name }} </span>
										<span class="message">{{ msg.message }}</span>
										<span class="author" ng-if="msg.name == $root.subscriber.metadata.name">Me </span>
									</div>
									
								</div>
								<form ng-submit="send()" novalidate class="pg-mt-20" style="margin: 0;">
									<div class="pg-row">
										<div class="pg-col-9">
											<div class="pg-text">
												<div>
													<input type="text" name="" ng-model="message" placeholder="Type you message" style="margin: 0;"/>
												</div>
											</div>
										</div>
										<div class="pg-col-3">
											<button type="submit" ng-disabled="sending" class="wingman-btn wingman-btn-small btn-block">{{ sending ? '...' : 'Send' }}</button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<!--<button class="wingman-btn wingman-btn-small wingman-btn-act pg-mt-20">View full log</button>-->
					</div>
					<div class="pg-col-6">
						<!--
						<h4>Traffic report</h4>
						<div class="pg-pad-20 pg-pad-20 pg-border">
							<div class="pg-row">
								<div class="pg-col-6 pg-totals-averages">
									<div class="new-customers-ico"></div>
									<div class="pg-totals-averages-info" id="total-recent-sales-div" style="padding-top: 6px;">
										<span class="totals-averages-heading" id="total-recent-sales">
											53%
										</span>
										<span class="totals-averages-title">New Users</span>
										<span class="totals-averages-last-days" id="total-recent-sales-lbl">Last 30 days</span>
									</div>
								</div>
								<div class="pg-col-6 pg-totals-averages">
									<div class="average-sales-ico"></div>
									<div class="pg-totals-averages-info" id="total-recent-sales-div" style="padding-top: 6px;">
										<span class="totals-averages-heading" id="total-recent-sales">
											1,746
										</span>
										<span class="totals-averages-title">Total Visits</span>
										<span class="totals-averages-last-days" id="total-recent-sales-lbl">Last 30 days</span>
									</div>
								</div>
							</div>
							<div id="chart" class="pg-mt-20"></div>
						</div>
						-->
						<h4>
							Report files
						</h4>
						<div class="">
							<div class="pg-text">
								<div class="pg-border pg-pad-20 pg-mb-20 files pg-white-bckg">
									<div ng-if="loading">Loading...</div>
									<div ng-if="!files.length && !loading">No files yet</div>
									<div ng-repeat="file in files">
										<a ng-href="{{ file.url }}" target="_blank">{{ file.name }} <sup ng-show="$index == 0">NEW</sup></a> 
										<a ng-href="{{ file.url }}" target="_blank" class="wingman-btn wingman-btn-small-x wingman-btn-act">Download</a>
									</div>
								</div>
								<!--<button class="wingman-btn wingman-btn-small wingman-btn-act">View all files</button>-->
							</div>
						</div>
					</div>
				</div>
				
			</div>
			
			<!-- Keywords -->
			<div id="tab-2" class="pg-mt-20">
				<!--<h4>You have </h4>-->
				
				<form name="keywordsForm" id="keywordsForm" novalidate>
					<formly-form model="subscription" fields="fields.inputs">
						<div class="pg-mt-20">
							<button type="button" class="wingman-btn" ng-show="!loading" ng-click="updateSubscription()">Save</button>
							<span data-ng-show="loading">Loading</span>
						</div>
						<messages messages="messages" class="pg-mt-20"></messages>
					</formly-form>
				</form>
			</div>
			
			<!-- My account information and update -->
			<div id="tab-3" class="pg-mt-20">
				<div class="pg-row">
					<div class="pg-col-6">
						<form name="myAccountForm" id="myAccountForm" data-ng-submit="update()" novalidate>
							<formly-form model="subscription" fields="fields.information">
								<div class="">
									<button type="submit" class="wingman-btn" data-ng-show="!loading">Update</button>
									<span data-ng-show="loading">Loading</span>
								</div>
							</formly-form>
							<messages messages="messages" class="pg-mt-20"></messages>
						</form>
					</div>
					<div class="pg-col-3">
						<p class="pg-mb-20">Your customer ID:<br><strong>{{ $root.subscriber.id }}</strong></p>
						<wingman-plan data-ng-repeat="plan in $root.subscriber.subscriptions.data" plan="plan.plan" class="pg-wingman-plan-featured wingman-plan-single"></wingman-plan>
						<button class="wingman-btn wingman-btn-warning wingman-btn-small pg-mt-20 btn-block" ng-click="unsubscribe()"> Cancel Subscription </button>
					</div>
				</div>
			</div>
			
			<!-- Contact page include 
			<div id="tab-4" class="pg-mt-20">
				<ng-include src="contactUsUrl"></ng-include>
			</div>
			-->
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(function(){
		jQuery( ".hasTooltip" ).tooltip({
			html: true,
			container: "body"
		});
	});
</script>