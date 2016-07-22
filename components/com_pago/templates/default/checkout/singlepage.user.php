<fieldset>
	
	<div data-ng-show="!user.id">
		
		<div class="row">
			
			<div data-ng-class="{'col-sm-12': !showLogin(), 'col-sm-6': showLogin()}"  data-ng-if="showOptions()">
				<legend>{{$root.config.language.PAGO_CHECKOUT_CHECKOUT_OPTION_NEW_CUSTOMER}}</legend>
				<p data-ng-show="showRegistrationOption()">{{$root.config.language.PAGO_CHECKOUT_CHECKOUT_INFO}}</p>
				<label class="radio" data-ng-show="showRegistrationOption()">
					<input type="radio" name="userType" data-ng-model="$root.userType" value="1">
					<span>{{$root.config.language.PAGO_CHECKOUT_REGISTER_ACCOUNT}}</span>
				</label>
				<label class="radio" data-ng-show="showGuestOption()">
					<input type="radio" name="userType" data-ng-model="$root.userType" value="2">
					<span>{{$root.config.language.PAGO_CHECKOUT_CHECKOUT_AS_GUEST}}</span>
				</label>
				<!--<button type="button" data-ng-click="$root.userType = ut">{{$root.config.language.PAGO_CHECKOUT_CONTINUE_BUTTON}}</button>-->
			</div>
			
			<div class="col-sm-12" data-ng-form="registerForm" data-ng-if="showRegistration()" data-ng-submit="register( registerForm )" novalidate>
				<legend>{{$root.config.language.PAGO_CHECKOUT_CHECKOUT_OPTION_NEW_CUSTOMER}}</legend>
				<div class="row">
					<label class="col-sm-6">
						<span>{{$root.config.language.PAGO_ACCOUNT_NAME}}</span>
						<input type="text" name="name" data-ng-model="$root.user.name" required tabindex="1"/>
						<div data-ng-messages="registerForm.name.$error" data-ng-show="registerForm.name.$touched || registerForm.$submitted">
							<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
						</div>
					</label>
					<label class="col-sm-6">
						<span>{{$root.config.language.PAGO_EMAIL}}</span>
						<input type="email" name="email" data-ng-model="$root.user.email" required tabindex="2"/>
						<div data-ng-messages="registerForm.email.$error" data-ng-show="registerForm.email.$touched || registerForm.$submitted">
							<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
						</div>
					</label>
				</div>
				<div class="row">
					<label class="col-sm-6">
						<span>{{$root.config.language.PAGO_ACCOUNT_LOGIN_PASSWORD}}</span>
						<input type="password" name="password" data-ng-model="$root.user.password" data-ng-required="!$root.user.id" minlength="3" tabindex="3"/>
						<div data-ng-messages="registerForm.password.$error" data-ng-show="registerForm.password.$touched || registerForm.$submitted">
							<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
						</div>
					</label>
					<label class="col-sm-6">
						<span>{{$root.config.language.PAGO_ACCOUNT_CONFIRM_PASSWORD}}</span>
						<input type="password" name="passwordMatch" data-ng-model="$root.user.passwordMatch" data-ng-required="!$root.user.id" data-match="user.password" minlength="3" tabindex="4"/>
						<div data-ng-messages="registerForm.passwordMatch.$error" data-ng-show="registerForm.passwordMatch.$touched || registerForm.$submitted">
							<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
						</div>
					</label>
				</div>
				<div data-ng-show="!loading">
					<button type="button" data-ng-click="register( registerForm )">{{$root.config.language.PAGO_ACCOUNT_REGISTER_BUTTON}}</button>
					<a href="javascript:void(0);" data-ng-click="$root.userType = null" class="pg-btn pg-btn-bordered">{{$root.config.language.PAGO_CANCEL_BUTTON}}</a>
				</div>
				<div data-ng-show="loading">
					<p class="muted">Loading...</p>
				</div>
				<alert type="$root.alert.type" message="$root.alert.message"></alert>
			</div>
			
			<div data-ng-class="{'col-sm-12': !showOptions(), 'col-sm-6': showOptions()}" data-ng-form="loginForm" data-ng-if="showLogin()" data-ng-submit="login(loginForm)" novalidate>
				<legend>{{$root.config.language.PAGO_CHECKOUT_CHECKOUT_OPTION_RETURNING_CUSTOMER}}</legend>
				<label >
					<span>{{$root.config.language.PAGO_ACCOUNT_LOGIN_USERNAME}}</span>
					<input type="text" name="username" data-ng-model="$root.user.username" required/>
					<div data-ng-messages="loginForm.username.$error" data-ng-show="loginForm.$submitted || loginForm.username.$touched">
						<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
					</div>
				</label>
				<label>
					<span>{{$root.config.language.PAGO_ACCOUNT_LOGIN_PASSWORD}}</span>
					<input type="password" name="password" data-ng-model="$root.user.password" data-ng-required="!$root.user.id" minlength="3"/>
					<div data-ng-messages="loginForm.password.$error" data-ng-show="loginForm.$submitted || loginForm.password.$touched">
						<div data-ng-messages-include="{{$root.baseTmplUrl}}singlepage.messages.php"></div>
					</div>
				</label>
				<label class="checkbox">
					<input type="checkbox" name="remember-me" data-ng-model="rememberMe">
					<span>{{$root.config.language.PAGO_ACCOUNT_LOGIN_REMEMBER_ME}}</span>
				</label>
				<div data-ng-show="!loading">
					<button type="button" data-ng-click="login(loginForm)">Login</button>
					<a href="{{passUrl}}" title="{{$root.config.language.PAGO_ACCOUNT_LOGIN_FORGOT_YOUR_PASSWORD}}" class="pg-btn pg-btn-bordered" data-ng-click="$parent.resetPassword = true">{{$root.config.language.PAGO_ACCOUNT_LOGIN_FORGOT_YOUR_PASSWORD}}</a>
				</div>
				<div data-ng-show="loading">
					<p class="muted">Loading...</p>
				</div>
				<alert type="$root.alert.type" message="$root.alert.message"></alert>
			</div>
			
		</div>
		
	</div>
	
</fieldset>