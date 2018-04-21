module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        release: {
            options: {
                additionalFiles: ['composer.json'],
                npm: false
            }
        },
        composer : {
          options : {
            usePhp: false,
            composerLocation: './composer'
          },
          default: {
            options : {
              cwd: '.'
            }
          }
        }
    });

    grunt.registerTask("default", ["composer:default:install"]);
    grunt.registerTask("build", ["composer:default:install"]);

    grunt.registerTask("dev", ["default"]);

    // needed modules
    grunt.loadNpmTasks('grunt-composer');
    grunt.loadNpmTasks('grunt-release');
}
