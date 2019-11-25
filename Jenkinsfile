
pipeline {
    agent any

    stages {
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
