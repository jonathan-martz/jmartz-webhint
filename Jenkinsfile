
pipeline {
    agent any

    stages {
        stage('Load config') {
            steps {
                sh 'robo load:config'
            }
        }

        stage('Npm install') {
            steps {
                sh 'robo npm:install'
            }
        }
        stage('Webhint') {
            steps {
                sh 'robo execute jmartz.de'
            }
        }
        stage('Copy reports') {
            steps {
                sh 'robo copy'
            }
        }
    }
}
