# Music Releases API
This API allows its users to subscribe to musical bands and receive emails and/or telegram (currently not implemented) notifications about their new releases.

## Technologies
The API is powered by symfony framework and runs a postgres DB via a docker container. The authentication is token-based. The app uses a remote database provided by Discogs API which can easily be switched to use any other API.  

## Updates
In order to update the local database and notify users you need to set up a crontab to run update and notify commands from the src/Command directory.
