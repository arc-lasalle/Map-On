module.exports = function(grunt) {

    // http://gruntjs.com/getting-started
    // https://github.com/gruntjs/grunt-contrib-uglify

    var js_path = "../../../public/js/external/dbgraph/";
    var css_path = "../../../public/css/external/dbgraph/";
    var img_path = "../../../public/img/external/dbgraph/";
    var deploy_path = "../../../public/dbgraph_deploy/";


    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        uglify: {

            step1: {
                options: {
                    compress: false,
                    mangle: false,
                    beautify: false
                },
                files: {
                    'temp/wwwsqldesigner.min.js': [
                        'wwwsqldesigner/js/oz.js',
                        'wwwsqldesigner/js/config.js',
                        'wwwsqldesigner/js/globals.js',
                        'wwwsqldesigner/js/visual.js',
                        'wwwsqldesigner/js/row.js',
                        'wwwsqldesigner/js/table.js',
                        'wwwsqldesigner/js/relation.js',
                        'wwwsqldesigner/js/key.js',
                        'wwwsqldesigner/js/rubberband.js',
                        'wwwsqldesigner/js/map.js',
                        'wwwsqldesigner/js/toggle.js',
                        'wwwsqldesigner/js/io.js',
                        'wwwsqldesigner/js/tablemanager.js',
                        'wwwsqldesigner/js/options.js',
                        'wwwsqldesigner/js/rowmanager.js',

                        'wwwsqldesigner/js/keymanager.js',
                        'wwwsqldesigner/js/window.js',
                        'wwwsqldesigner/js/wwwsqldesigner.js',

                        'app/js/draggable.js',
                        'app/js/mapon_dbgraph.js',]
                }
            }

        },

        cssmin: {

            options: {
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    'temp/wwwsqldesigner.css': [
                        'temp/wwwsqldesigner.css'
                    ]
                }
            }

        },

        css_selectors: {

            step1: {
                options: {
                    mutations: [
                        {prefix: '.dbgraph'}
                    ]
                },
                files: {
                    'temp/wwwsqldesigner.css': [
                        'wwwsqldesigner/styles/style.css',
                        'app/css/mapon_dbgraph.css'
                    ],
                },
            },

        },

        copy: {

            step2: {
                files: [
                    {expand: true, cwd: "temp", src: ["wwwsqldesigner.min.js"], dest: js_path},
                    {expand: true, cwd: "wwwsqldesigner/db/", src: ['**'], dest: js_path + '/db'},
                    {expand: true, cwd: "temp", src: ["wwwsqldesigner.css"], dest: css_path},
                    {expand: true, cwd: "wwwsqldesigner/images", src: ["back.png"], dest: img_path},
                ]
            },

            step2_deploy: {
                files: [
                    {expand: true, cwd: "temp", src: ["wwwsqldesigner.min.js"], dest: deploy_path},
                    {expand: true, cwd: "wwwsqldesigner/db/", src: ['**'], dest: deploy_path + '/db'},
                    {expand: true, cwd: "temp", src: ["wwwsqldesigner.css"], dest: deploy_path},

                    {expand: true, cwd: "app", src: ["index.html"], dest: deploy_path},
                ]
            }

        },

        clean: {
            build: {
                src: ["temp"]
            }
        }
    });


    // Load the plugin that provides the "uglify" task.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-css-selectors');
    grunt.loadNpmTasks('grunt-contrib-clean');

    // tasks

    grunt.registerTask('uglify_files', [ 'uglify:step1', 'css_selectors:step1', 'cssmin' ]);
    grunt.registerTask('copy_files', [ 'copy:step2', 'copy:step2_deploy']);
    grunt.registerTask('clean_temp', [ 'clean' ]);

    grunt.registerTask('default', ['uglify_files', 'copy_files', 'clean_temp']);

};