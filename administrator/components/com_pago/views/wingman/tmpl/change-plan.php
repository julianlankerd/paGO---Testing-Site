<section>
	<h2>You can upgrade or downgrade your plan anytime:</h2>
	<div data-ng-show="!plans" class="pg-col-12">
		<h4>Loading...</h4>
	</div>
	<div class="pg-wingman-plan-wrapper">
		<wingman-plan data-ng-repeat="plan in plans | filter:interval | orderBy:'+amount'" data-plan="plan" data-ng-class="{'pg-wingman-plan-featured': plan.metadata.top_seller && '/plans' == $root.path, 'pg-wingman-plan-current': $root.subscriber.subscriptions.data[0].plan.id == plan.id}"></wingman-plan>
		<wingman-plan data-plan="customPlan" data-ng-show="plans"></wingman-plan>
	</div>
	<p class="text-center pg-mt-20">
		<a href="#/dashboard" class="wingman-btn wingman-btn-small wingman-btn-act">Nevermind, take me back</a>
	</p>
</section>