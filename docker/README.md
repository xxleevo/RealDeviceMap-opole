# RaidBillBoard Docker Image

Builds docker image with apache-php-composer and versx's fork of [RealDeviceMap-RaidBillBoard](https://github.com/versx/RealDeviceMap-RaidBillBoard)

## Requirements

- docker

## Instructions

1. Follow setup instructions [README](https://github.com/versx/RealDeviceMap-RaidBillBoard)
	* Update `config.php`
	* Edit .txt files in `geofences` folder
3. Run docker build (ex. `docker build . -t rdmopole`)
4. Run docker image (ex. `docker run -p 8080:80 -d rdmopole`)
5. **Recommended:** Setup reverse proxy to docker image
