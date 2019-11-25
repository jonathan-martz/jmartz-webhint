
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
        stage('jmartz.de') {
            steps {
                sh 'robo execute jmartz.de'
            }
        }
        stage('copy reports') {
            steps {
                sh 'robo copy'
            }
        }
    }
}
