module.exports = function (grunt) {
    var MODULE_NAME = "rinsvent.fastauth";
    grunt.initConfig({
        concat: {
            core: {
                src: [
                    "../install/js/admin/**/*.js"
                ],
// dest: "../../../js/" + MODULE_NAME + ""
            },
            lib: {}
        },
        copy: {
            scripts: {
                cwd: "../install/js/admin",
                src: "**/*",
                dest: "../../../js/" + MODULE_NAME + "/admin",
                expand: true
            },
            styles: {
                cwd: "../install/themes/.default",
                src: "**/*",
                dest: "../../../css/" + MODULE_NAME + "/admin",
                expand: true
            }
        },
        uglify: {
            core: {
                src: "../../../js/" + MODULE_NAME + "/admin/core.js",
                dest: "../../../js/" + MODULE_NAME + "/admin/core.min.js"
            }
        },
        watch: {
            scripts: {
                files: ["../install/js/admin/**/*.js"],
                tasks: ["copy"],
                options: {
                    spawn: false
                }
            },
            styles: {
                files: ["../install/themes/.default/**/*.css"],
                tasks: ["copy"],
                options: {
                    spawn: false
                }
            }
        }
    });

    require('load-grunt-tasks')(grunt);
    grunt.registerTask("default", ["copy"]);
    grunt.registerTask("run-watch", ["watch"]);
};