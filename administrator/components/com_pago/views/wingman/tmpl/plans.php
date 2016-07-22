<!--
<div data-ng-show="!plans" class="pg-col-12">
	<p>Loading...</p>
</div>
<div class="pg-col-12 pg-mb-20">
	<p>Show me prices by</p>
	<div class="pg-radio">
		<input type="radio" data-ng-model="interval" name="interval" id="interval_month" value="month">
		<label for="interval_month"></label>
		<span>Month</span>
	</div>
	<div class="pg-radio">
		<input type="radio" data-ng-model="interval" name="interval" id="interval_year" value="year">
		<label for="interval_year"></label>
		<span>Year</span>
	</div>
</div>
-->
<div class="pg-white-bckg">

<header>
	<img src="{{ comurl }}css/wingman/img/logo-headernav.png" alt="SEO Wingman">
	<h1>is a powerful SEO as a Service</h1>
	<h3>Every month we work with your unique keywords and drive qualified traffic to your site. It is as easy as that.</h3>
</header>

<section id="the-process">
	<h2>Sign Up is a simple 3 step process:</h2>
	<div class="pg-row">
		<div class="pg-col-4">
			<div class="bullet">1</div>
			<h4>Choose a package</h4>
		</div>
		<div class="pg-col-4">
			<div class="bullet">2</div>
			<h4>Input your information</h4>
		</div>
		<div class="pg-col-4">
			<div class="bullet">3</div>
			<h4>Input target keywords and your competition</h4>
		</div>
	</div>
	<p>SEO Wingman™ is an integrated tool that allows our experts to assist you in improving and maintaining your site’s search engine optimization. It’s great for both web professionals and small business owners because it offloads the tedious, time consuming part of SEO and lets you focus on what you do best.</p>
</section>

<section id="pricing">
	<h2>Our Pricing:</h2>
	<div data-ng-show="!plans" class="pg-col-12">
		<h4>Loading...</h4>
	</div>
	<div class="pg-wingman-plan-wrapper">
		<wingman-plan data-ng-repeat="plan in plans | filter:interval | orderBy:'+amount'" data-plan="plan" data-ng-class="{'pg-wingman-plan-featured': plan.metadata.top_seller && '/plans' == $root.path}"></wingman-plan>
		<wingman-plan data-plan="customPlan" data-ng-show="plans"></wingman-plan>
	</div>
</section>

<footer>
	<h1>Together, we bring a solution that will have a positive impact on your ROI.</h1>
	<h3>Click below for more details on each of our packages.</h3>
	<a href="http://seowingman.com" target="_blank" class="wingman-btn wingman-btn-alt">Learn More</a>
</footer>

</div>