module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),


    });


    grunt.registerTask("default", ["composer:install"]);
    grunt.registerTask("build", ["composer:install"]);

    grunt.registerTask("dev", ["default"]);

    // needed modules
    grunt.loadNpmTasks('grunt-composer');
    grunt.loadNpmTasks('grunt-release');
}
