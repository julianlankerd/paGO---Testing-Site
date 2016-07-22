<div class="pg-col-6">
	<form name="contactForm" id="contactForm" data-ng-submit="contact()" novalidate>
		<div class="pg-wingman-subscribe">
			<h2>Contact Us</h2>
			<div class="">
				
				<formly-form model="contact" fields="fields.contact">
					<div class="pg-mt-20">
						<button type="submit" class="wingman-btn" data-ng-show="!loading">Send</button>
						<span data-ng-show="loading">Loading</span>
					</div>
					<messages messages="messages" class="pg-mt-20"></messages>
				</formly-form>
				
			</div>
		</div>
	</form>
</div>

<div class="pg-col-6" ng-if="subscriber.id && $root.path != '/dashboard'">
	<a href="#/dashboard" class="wingman-btn wingman-btn-act wingman-btn-small">Nevermind, take me back</a>
</div>