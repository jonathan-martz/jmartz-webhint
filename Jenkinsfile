
pipeline {
    agent any

    stages {
        stage('Download config') {
            steps {
                sh 'robo download:config'
            }
        }

        stage('Npm install') {
            steps {
                sh 'robo npm:install'
            }
        }
        stage('Webhint') {
            steps {
                sh 'robo execute'
            }
        }
        stage('Copy reports') {
            steps {
                sh 'robo copy'
            }
        }
    }
}
