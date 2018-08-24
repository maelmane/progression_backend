docker-compose down 
docker-compose build
docker-compose up -d progression_db
sleep 5 && docker exec progression_db bash -c "cd /tmp/ && ./build_db.sh"
docker-compose up -d progression
