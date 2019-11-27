
pipeline {
    agent any

    stages {
        stage('Download config') {
            steps {
                sh 'robo download:config'
            }
        }

        stage('Install dependencies') {
            steps {
                sh 'robo npm:install'
                sh 'robo composer:install'
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
