@Library(['mysql', 'slack']) _
import org.gradiant.jenkins.slack.*

pipeline {
  agent any
  environment {
    MYSQLHOST = 'localhost'
    $DRUPALADMINUSER = 'd7admin'
    DRUPALADMINUSERPASS = 'horse-staple-battery'
    DRUPALSITENAME = 'My Corona Site'
    DRUPALSITEMAIL = 'drupal@fastglass.net'
    SLACK_CHANNEL = 'cicd'
    SLACK_DOMAIN = 'fastglass'
    CHANGE_LIST = 'true'
    TEST_SUMMARY = 'true'
  }

  parameters {
    string(name: 'baseSiteDbName', defaultValue: '')
    string(name: 'baseSiteDbUser', defaultValue: '')
    string(name: 'baseSiteDbUserPass', defaultValue: '')
    string(name: 's1SiteSubDir', defaultValue: 's1.corona.com')
    string(name: 's1SiteDbName', defaultValue: '')
    string(name: 's1SiteDbUser', defaultValue: '')
    string(name: 's1SiteDbUserPass', defaultValue: '')
    string(name: 's2SiteSubDir', defaultValue: 's2.corona.com')
    string(name: 's2SiteDbName', defaultValue: '')
    string(name: 's2SiteDbUser', defaultValue: '')
    string(name: 's2SiteDbUserPass', defaultValue: '')
  }

  stages {
    stage('Clone Repo') {
      steps {
        script {
          new SlackNotifier().notifyStart()
        }
        retry(3) {
          checkout scm
        }
        script {
          def commitHash = checkout(scm).GIT_COMMIT
          echo "Commit Hash is ${commitHash}"
        }
      }
    }
    stage('Composer CC') {
      steps {
        sh 'composer clear-cache'
      }
    }
    stage('Install Base') {
      steps {
        script {
          withCredentials([usernamePassword(credentialsId: 'mysql-root', passwordVariable: 'DATABASE_PASSWORD', usernameVariable: 'DATABASE_USERNAME')]) {
            def dbrootuser = env.DATABASE_USERNAME
            def dbrootpass = env.DATABASE_PASSWORD
            def base_database_credentials_base = buildTestMySQLDatabase {
              dbUser = dbrootuser
              dbPass = dbrootpass
            }
            env.baseSiteDbName = base_database_credentials_base.dbName
            env.baseSiteDbUser = base_database_credentials_base.testUsername
            env.baseSiteDbUserPass = base_database_credentials_base.testUserPassword
          } // withCredentials
          echo "============================================ BASE SITE ======================================================"
          echo 'Test Database Name: ' + env.baseSiteDbName
          echo 'Test Username: ' + env.baseSiteDbUser
          echo "Starting Drupal Install"
          sh 'chmod u+x ./install.drush.sh'
          sh 'bash ./install.drush.sh -g $MYSQLHOST -i ' + env.baseSiteDbUser + ' -j ' + env.baseSiteDbUserPass + ' -n ' + env.baseSiteDbName + ' -d $DRUPALADMINUSER -e $DRUPALADMINUSERPASS -t "$DRUPALSITENAME" -u "$DRUPALSITEMAIL" -x "yes"'
          echo "============================================ /BASE SITE ======================================================"
        }
      }
    } // Install base
    stage('Install S1') {
      steps {
        script {
          withCredentials([usernamePassword(credentialsId: 'mysql-root', passwordVariable: 'DATABASE_PASSWORD', usernameVariable: 'DATABASE_USERNAME')]) {
            def dbrootuser = env.DATABASE_USERNAME
            def dbrootpass = env.DATABASE_PASSWORD
            def s1_database_credentials_base = buildTestMySQLDatabase {
              dbUser = dbrootuser
              dbPass = dbrootpass
            }
            env.s1SiteDbName = s1_database_credentials_base.dbName
            env.s1SiteDbUser = s1_database_credentials_base.testUsername
            env.s1SiteDbUserPass = s1_database_credentials_base.testUserPassword
          } // withCredentials
          echo "============================================ S1 SITE ======================================================"
          echo 'Test Database Name: ' + env.s1SiteDbName
          echo 'Test Username: ' + env.s1SiteDbUser
          echo "Starting Drupal Install"
          sh 'bash ./install.drush.sh -g $MYSQLHOST -i ' + env.s1SiteDbUser + ' -j ' + env.s1SiteDbUserPass + ' -n ' + env.s1SiteDbName + ' -d $DRUPALADMINUSER -e $DRUPALADMINUSERPASS -t "$DRUPALSITENAME" -u "$DRUPALSITEMAIL" -s ' + env.s1SiteSubDir
          echo "============================================ /S1 SITE ======================================================"
        }
      }
    } // Install S1
    stage('Install S2') {
      steps {
        script {
          withCredentials([usernamePassword(credentialsId: 'mysql-root', passwordVariable: 'DATABASE_PASSWORD', usernameVariable: 'DATABASE_USERNAME')]) {
            def dbrootuser = env.DATABASE_USERNAME
            def dbrootpass = env.DATABASE_PASSWORD
            def s2_database_credentials_base = buildTestMySQLDatabase {
              dbUser = dbrootuser
              dbPass = dbrootpass
            }
            env.s2SiteDbName = s2_database_credentials_base.dbName
            env.s2SiteDbUser = s2_database_credentials_base.testUsername
            env.s2SiteDbUserPass = s2_database_credentials_base.testUserPassword
          } // withCredentials
          echo "============================================ S2 SITE ======================================================"
          echo 'Test Database Name: ' + env.s2SiteDbName
          echo 'Test Username: ' + env.s2SiteDbUser
          echo "Starting Drupal Install"
          sh 'bash ./install.drush.sh -g $MYSQLHOST -i ' + env.s2SiteDbUser + ' -j ' + env.s2SiteDbUserPass + ' -n ' + env.s2SiteDbName + ' -d $DRUPALADMINUSER -e $DRUPALADMINUSERPASS -t "$DRUPALSITENAME" -u "$DRUPALSITEMAIL" -s ' + env.s2SiteSubDir
          echo "============================================ /S2 SITE ======================================================"
        }
      }
    } // Install S2
    stage('Unit Tests') {
      steps {
        script {
          sh 'composer update phpunit/phpunit phpspec/prophecy symfony/yaml --with-dependencies --no-progress'
          try {
            sh './vendor/bin/phpunit --testsuite=unit -c web/core/'
          }
          catch (e) {
            new SlackNotifier().notifyError(e)
          }
        }
      }
    }
  }
  post {
    always {
      script {
        sh 'chmod -R 777 web/sites/default'
        withCredentials([usernamePassword(credentialsId: 'mysql-root', passwordVariable: 'DATABASE_PASSWORD', usernameVariable: 'DATABASE_USERNAME')]) {
          def dbrootuser = env.DATABASE_USERNAME
          def dbrootpass = env.DATABASE_PASSWORD
          echo "Tear down Base"
          destroyTestMySQLDatabase (dbrootuser, dbrootpass, env.baseSiteDbName, env.baseSiteDbUser)
          echo "Done Base"
          echo "Tear down Subsite 1"
          destroyTestMySQLDatabase (dbrootuser, dbrootpass, env.s1SiteDbName, env.s1SiteDbUser)
          echo "Done S1"
          echo "Tear down Subsite 2"
          destroyTestMySQLDatabase (dbrootuser, dbrootpass, env.s2SiteDbName, env.s2SiteDbUser)
          echo "Done S2"
        } // withCredentials
        new SlackNotifier().notifyResultFull()
        // If permissions are not changes Jenkins will not be able to clean the workspace.
        cleanWs()
      } // script
      cleanWs()
    } // always
  }
}

