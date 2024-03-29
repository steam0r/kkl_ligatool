module.exports = function (grunt){

  let conf = {
      cssCwd: 'css',
      cssDest: 'css',

      jsCwd: 'js',
      jsDest: 'js'
    };

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    cmp: grunt.file.readJSON('composer.json'),

    // compile sass > css > css.min
    // ==================================
    sass: {
      dist: {
        options: {
          sourceMap: false,
          outputStyle: 'compressed', // expanded, compressed, compact, nested
          includePaths: [conf.cssCwd]
        },

        files: [{
          expand: true,
          cwd: conf.cssCwd + '/',
          src: ['ligatool.scss'],
          dest: conf.cssDest + '/',
          ext: '.css'
        }]
      }
    },

    postcss: {
      options: {
        processors: [
          require('autoprefixer') // add vendor prefixes
        ]
      },
      dist: { // = distPortal
        src: conf.cssDest + '/*.css'
      }
    },

    // ===================================
    watch: {
      css: {
        files: [conf.cssCwd + '/**/*.scss'],
        tasks: ['sass', 'postcss'],
        options: {
          spawn: false
        }
      }
    },

    bumpup: {
      options: {
        updateProps: {
          pkg: 'package.json'
        }
      },
      files: ['package.json', 'composer.json']
    },
    composer: {
      options: {
        usePhp: true,
        composerLocation: './composer'
      },
      default: {
        options: {
          cwd: '.'
        }
      }
    },
    copy: {
      update: {
        files: [
          {
            expand: true,
            src: ['css/**', 'js/**', 'images/**', 'lang/**', 'pages/**', 'sql/**', 'src/**', 'templates/**', 'vendor/**', 'kkl_ligatool.php', 'README.md'],
            dest: '<%= cmp.name %>/'
          }
        ]
      }
    },
    compress: {
      main: {
        options: {
          archive: 'target/kkl_ligatool.zip',
          mode: 'zip'
        },
        expand: true,
        src: ['css/**', 'js/**', 'images/**', 'lang/**', 'pages/**', 'sql/**', 'src/**', 'templates/**', 'vendor/**', 'kkl_ligatool.php', 'README.md']
      },
      update: {
        options: {
          archive: 'target/update/kkl_ligatool.zip',
          mode: 'zip'
        },
        src: [
          '<%= cmp.name %>/**'
        ]
      }
    },
    replace: {
      versions: {
        options: {
          patterns: [
            {
              match: /Version:\s*(.*)/,
              replacement: 'Version: <%= pkg.version %>'
            }
          ]
        },
        files: [
          {
            expand: true,
            flatten: true,
            src: ['README.md', 'kkl_ligatool.php']
          }
        ]
      }
    },
    clean: [
      'target',
      'vendor',
      'target',
      '<%= cmp.name %>',
      'vendor',
      'release.json'
    ]
  });

  // Alias task for release
  grunt.registerTask('release', function (type){
    grunt.task.run('clean');        // clean previous builds
    type = type ? type : 'patch';     // default release type
    grunt.task.run('bumpup:' + type); // bump up the version
    grunt.task.run('replace');        // replace version number in plugin file and readme
    grunt.task.run('composer:default:install');         // get php dependencies
    grunt.task.run('compress');     // build a release zip
    grunt.task.run('compress:main');     // build a release zip
    grunt.task.run('copy:update'); // copy files over to build an update zip
    grunt.task.run('compress:update');     // build a release zip
  });

  // Alias task for release with buildmeta suffix support
  grunt.registerTask('release', function (type, build){
    grunt.task.run('clean');        // clean previous builds
    var bumpParts = ['bumpup'];
    if (type){
      bumpParts.push(type);
    }
    if (build){
      bumpParts.push(build);
    }
    grunt.task.run(bumpParts.join(':')); // bump up the version
    grunt.task.run('replace');        // replace version number in plugin file and readme
    grunt.task.run('composer:default:install');         // get php dependencies
    grunt.task.run('compress:main');     // build a release zip
    grunt.task.run('copy:update'); // copy files over to build an update zip
    grunt.task.run('compress:update');     // build a release zip
  });

  grunt.registerTask('strider:releasefile', function (type){
    var artifcatId = process.env.STRIDER_ARTIFACT_ID;
    var branch = process.env.STRIDER_BRANCH;
    if (!artifcatId || !branch){
      grunt.fail.fatal("got no information in environment: STRIDER_ARTIFACT_ID or STRIDER_BRANCH missing!", 1);
    }
    var name = process.env.STRIDER_PROJECT_NAME;
    var cmp = grunt.config.get("cmp");
    var projectName = name ? name : cmp.name;
    var releaseJson = {
      "name": projectName,
      "version": cmp.version,
      "download_url": cmp.extra.release.baseurl + projectName + "/api/artifact-repository/dl/" + artifcatId + "?branch=" + branch,
      "sections": {
        "description": cmp.description
      }
    };
    grunt.file.write('release.json', JSON.stringify(releaseJson, null, 2));
  });

  grunt.registerTask('build', ['clean', 'composer:default:install']);
  grunt.registerTask('package', ['default', 'compress:main', 'copy:update', 'compress:update']);
  grunt.registerTask('default', ['build']);

  grunt.registerTask('dev', ['sass', 'postcss', 'watch']);

  // needed modules
  grunt.loadNpmTasks('grunt-composer');
  grunt.loadNpmTasks('grunt-replace');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-bumpup');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-postcss');
  grunt.loadNpmTasks('grunt-sass');
};