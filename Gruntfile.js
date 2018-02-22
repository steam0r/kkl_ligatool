module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),

        "release-it": {
            options: {
                pkgFiles: ['package.json'],
                src: {
                    commitMessage: 'Release %s',
                    tagName: '%s',
                    tagAnnotation: 'Release %s'
                },
                npm: {
                    publish: false
                },
                github: {
                    release: true,
                    assets: "vendor"
                }
            }
        }
    });

    grunt.registerTask("default", ["composer:install"]);
    grunt.registerTask("build", ["composer:install"]);

    grunt.registerTask("dev", ["default"]);

    // needed modules
    grunt.loadNpmTasks('grunt-composer');
    grunt.loadNpmTasks('grunt-release-it');
}
