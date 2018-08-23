
pipeline {
    agent any

    stages {
        stage('Npm install') {
            steps {
                sh 'robo execute npm-install'
            }
        }
        stage('jmartz.de') {
            steps {
                sh 'robo execute jmartz.de'
            }
        }
    }
}
