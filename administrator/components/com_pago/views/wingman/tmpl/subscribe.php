<div class="pg-col-6">
	<form name="subscribeForm" id="subscribeForm" data-ng-submit="subscribe()" novalidate>
		<div class="pg-wingman-subscribe">
			<h2>Subscription information</h2>
			<div class="pg-pad-20 ">
				
				<formly-form model="subscription" fields="fields.subscription">
					<div class="pg-mt-20">
						<button type="submit" class="wingman-btn" data-ng-show="!loading">Subscribe</button>
						<span data-ng-show="loading">Loading</span>
					</div>
					<messages messages="messages" class="pg-mt-20"></messages>
				</formly-form>
				
			</div>
		</div>
	</form>
</div>

<div class="pg-col-3">
	<wingman-plan data-plan="$root.plan" class="wingman-plan-single"></wingman-plan>
</div>