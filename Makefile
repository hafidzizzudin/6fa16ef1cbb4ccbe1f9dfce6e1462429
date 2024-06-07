include .env

migrate-up:
	migrate -path database/migration/ -database "mysql://$(DB_USERNAME):$(DB_PASSWORD)@tcp($(DB_HOST):$(DB_PORT))/$(DB_DATABASE)" -verbose up

migrate-down:
	migrate -path database/migration/ -database "mysql://$(DB_USERNAME):$(DB_PASSWORD)@tcp($(DB_HOST):$(DB_PORT))/$(DB_DATABASE)" -verbose down