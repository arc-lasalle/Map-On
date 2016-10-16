"use strict";

module.exports = function (grunt) {

	require('load-grunt-tasks')(grunt);
	var webpack = require("webpack");
	var webpackConfig = require("./webpack.config.js");

	var jsPath = "../../../public/js/external/webvowl/";
	var deployPath = "../../../public/webvowl_deploy/";
	var srcPath = "WebVOWL/src/";

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),
		bump: {
			options: {
				files: ["package.json"],
				updateConfigs: ["pkg"],
				commit: true,
				commitMessage: "Bump version to %VERSION%",
				commitFiles: ["package.json"],
				createTag: false,
				push: false
			}
		},
		clean: {
			deploy: deployPath,
			zip: "webvowl-*.zip",
			testOntology: deployPath + "data/benchmark.json"
		},
		compress: {
			deploy: {
				options: {
					archive: function() {
						var branchInfo = grunt.config("gitinfo.local.branch.current");
						return "webvowl-" + branchInfo.name + "-" + branchInfo.shortSHA + ".zip";
					},
					level: 9,
					pretty: true
				},
				files: [
					{expand: true, cwd: "deploy/", src: ["**"], dest: "webvowl/"}
				]
			}
		},
		connect: {
			devserver: {
				options: {
					protocol: "http",
					hostname: "localhost",
					port: 8000,
					base: deployPath,
					directory: deployPath,
					livereload: true,
					open: "http://localhost:8000/",
					middleware: function (connect, options, middlewares) {
						return middlewares.concat([
							require("serve-favicon")("deploy/favicon.ico"),
							require("serve-static")(options.base[0])
						]);
					}
				}
			}
		},
		copy: {
			deploy: {
				files: [
					{expand: true, cwd: "node_modules/d3/", src: ["d3.min.js"], dest: deployPath + "/js/"},
					{expand: true, cwd: srcPath, src: ["favicon.ico"], dest: deployPath},
					{expand: true, src: ["license.txt"], dest: deployPath}
				]
			},
			final: {
				files: [
					{expand: true, cwd: "node_modules/d3/", src: ["d3.min.js"], dest: jsPath},
					{expand: true, cwd: deployPath + "/js/", src: ["webvowl.js"], dest: jsPath },
					{expand: true, cwd: deployPath + "/js/", src: ["webvowl.app.js"], dest: jsPath }
				]
			}
		},
		htmlbuild: {
			options: {
				beautify: true,
				relative: true,
				data: {
					// Data to pass to templates
					version: "<%= pkg.version %>"
				}
			},
			dev: {
				src: "app/index.html",
				dest: deployPath
			},
			release: {
				// required for removing the benchmark ontology from the selection menu
				src: "app/index.html",
				dest: deployPath
			}
		},
		jshint: {
			options: {
				jshintrc: true
			},
			source: [srcPath + "**/*.js"],
			tests: ["test/*/**/*.js"]
		},
		karma: {
			options: {
				configFile: "test/karma.conf.js"
			},
			dev: {},
			continuous: {
				singleRun: true
			}
		},
		replace: {
			options: {
				patterns: [
					{
						match: "WEBVOWL_VERSION",
						replacement: "<%= pkg.version %>"
					}
				]
			},
			dist: {
				files: [
					{expand: true, cwd: "deploy/js/", src: "webvowl*.js", dest: "deploy/js/"}
				]
			}
		},
		webpack: {
			options: webpackConfig,
			build: {
				plugins: webpackConfig.plugins.concat(
					//new webpack.optimize.UglifyJsPlugin(),
					new webpack.optimize.DedupePlugin()
				)
			},
			"build-dev": {
				devtool: "sourcemap",
				debug: true
			}
		},
		watch: {
			configs: {
				files: ["Gruntfile.js"],
				options: {
					reload: true
				}
			},
			js: {
				files: [srcPath + "app/**/*", srcPath + "webvowl/**/*"],
				tasks: ["webpack:build-dev", "post-js"],
				options: {
					livereload: true,
					spawn: false
				}
			},
			html: {
				files: [srcPath + "**/*.html"],
				tasks: ["htmlbuild:dev"],
				options: {
					livereload: true,
					spawn: false
				}
			}
		}
	});


	grunt.registerTask("default", ["release"]);
	grunt.registerTask("pre-js", ["clean:deploy", "clean:zip", "copy"]);
	grunt.registerTask("post-js", ["replace"]);
	grunt.registerTask("package", ["pre-js", "webpack:build-dev", "post-js", "htmlbuild:dev"]);
	//grunt.registerTask("release", ["pre-js", "webpack:build", "post-js", "htmlbuild:release", "clean:testOntology"]);
	grunt.registerTask("release", ["copy:deploy", "webpack:build", "post-js", "htmlbuild:release", "copy:final"]);
	grunt.registerTask("zip", ["gitinfo", "release", "compress"]);
	grunt.registerTask("webserver", ["package", "connect:devserver", "watch"]);
	grunt.registerTask("test", ["karma:dev"]);
	grunt.registerTask("test-ci", ["karma:continuous"]);
};
