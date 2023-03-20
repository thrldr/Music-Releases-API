# Music Releases API
This API allows its users to subscribe to musical bands and receive emails and/or telegram (currently not implemented) notifications about their new releases.

## Technologies
The API is powered by symfony framework and runs a postgres DB in a docker container. The authentication is token-based. The app uses third-party database provided by Discogs API and can easily be switched to use any other API.  
