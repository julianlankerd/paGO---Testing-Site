module.exports = function( grunt ) {

	grunt.initConfig({
		concat: {
			basic: {
				src: [ 
					"node_modules/api-check/dist/api-check.min.js",
					"node_modules/angular/angular.min.js",
					"node_modules/angular-resource/angular-resource.min.js",
					"node_modules/angular-route/angular-route.min.js",
					"node_modules/angular-sanitize/angular-sanitize.min.js",
					"node_modules/angular-messages/angular-messages.min.js",
					"node_modules/angular-formly/dist/formly.min.js",
				],
				dest: "vendors.js",
			},
			extras: {
				src: [
					"source/wingman-variables.js",
					"source/wingman-resource.js",
					"source/wingman-formly.js",
					"source/wingman-stripe.js",
					"source/wingman-geolocation.js",
					"source/wingman-plans.js",
					"source/wingman-subscriber.js",
					"source/wingman-s3.js",
					
					// Get the ball rolling
					"source/wingman.js",
				],
				dest: "wingman.js"
			}
		},
		watch: {
			scripts: {
				files: "source/*.js",
				tasks: [ "concat" ]
			}
		}
		
	});
	
	grunt.loadNpmTasks( "grunt-contrib-concat" );
	grunt.loadNpmTasks( "grunt-contrib-watch" );

	grunt.registerTask( "default", [ "concat", "watch" ] );

};