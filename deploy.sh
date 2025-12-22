#!/bin/bash
# Deploy script para ADMCloud API em Docker
# Uso: ./deploy.sh [start|stop|restart|logs|status|clean]

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função de logging
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Função para verificar Docker
check_docker() {
    if ! command -v docker &> /dev/null; then
        log_error "Docker não está instalado!"
        exit 1
    fi
    if ! command -v docker-compose &> /dev/null; then
        log_error "Docker Compose não está instalado!"
        exit 1
    fi
    log_info "Docker e Docker Compose verificados ✓"
}

# Função para iniciar containers
start_containers() {
    log_info "Iniciando containers..."
    docker-compose up -d --build
    
    log_info "Aguardando health checks..."
    sleep 10
    
    log_info "Verificando status dos containers..."
    docker-compose ps
    
    log_info "✓ Containers iniciados com sucesso!"
    log_info "API disponível em: http://localhost:8080"
    log_info "phpMyAdmin disponível em: http://localhost:8081"
}

# Função para parar containers
stop_containers() {
    log_info "Parando containers..."
    docker-compose stop
    log_info "✓ Containers parados"
}

# Função para reiniciar containers
restart_containers() {
    log_warn "Reiniciando containers..."
    docker-compose restart
    sleep 5
    docker-compose ps
    log_info "✓ Containers reiniciados"
}

# Função para ver logs
show_logs() {
    if [ -z "$2" ]; then
        log_info "Mostrando logs de todos os serviços (Ctrl+C para sair)..."
        docker-compose logs -f
    else
        log_info "Mostrando logs de: $2"
        docker-compose logs -f "$2"
    fi
}

# Função para ver status
show_status() {
    log_info "Status dos containers:"
    docker-compose ps
    
    log_info ""
    log_info "Espaço em disco:"
    docker system df
    
    log_info ""
    log_info "Health checks:"
    docker-compose exec admcloud-api curl -s http://localhost/api/v1/passport?cgc=test || true
}

# Função para limpar
clean() {
    log_warn "Removendo containers e volumes..."
    read -p "Tem certeza? Isso vai deletar o banco de dados! (s/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Ss]$ ]]; then
        docker-compose down -v
        log_info "✓ Limpeza concluída"
    else
        log_warn "Operação cancelada"
    fi
}

# Função para testar API
test_api() {
    log_info "Testando endpoints da API..."
    
    log_info ""
    log_info "1. Teste GET /passport"
    curl -s "http://localhost:8080/api/v1/passport?cgc=01611275000205&hostname=DOCKER-TEST&guid=550e8400-e29b-41d4-a716-446655440000" | jq . || echo "ERRO na requisição"
    
    log_info ""
    log_info "2. Teste GET /registro"
    curl -s -u "api_frontbox:api_FBXzylXI0ZluneF1lt3rwXyZsfayp0cCrKCGX0rg" "http://localhost:8080/api/v1/registro" | jq . || echo "ERRO na requisição"
    
    log_info ""
    log_info "✓ Testes concluídos"
}

# Função para ver banco de dados
show_database() {
    log_info "Conectando ao banco de dados MySQL..."
    docker-compose exec -T mysql mysql -u papion -pPap10nL4vrAs2024 papion -e "
        SELECT '=== PESSOAS ===' as '';
        SELECT ID_PESSOA, CGC, NOME, ATIVO, EXPIRA_EM FROM PESSOAS LIMIT 5;
        
        SELECT '' as '';
        SELECT '=== PESSOA_LICENCAS ===' as '';
        SELECT ID, ID_PESSOA, HOSTNAME, GUID, STATUS FROM PESSOA_LICENCAS LIMIT 5;
    " || true
}

# Função principal
main() {
    case "${1:-status}" in
        start)
            check_docker
            start_containers
            ;;
        stop)
            stop_containers
            ;;
        restart)
            restart_containers
            ;;
        logs)
            check_docker
            show_logs "$@"
            ;;
        status)
            check_docker
            show_status
            ;;
        test)
            check_docker
            test_api
            ;;
        db)
            check_docker
            show_database
            ;;
        clean)
            check_docker
            clean
            ;;
        shell)
            if [ -z "$2" ]; then
                log_info "Abrindo shell do admcloud-api..."
                docker-compose exec admcloud-api bash
            else
                log_info "Abrindo shell de: $2"
                docker-compose exec "$2" bash
            fi
            ;;
        *)
            cat << EOF
Uso: $0 [COMANDO] [OPCOES]

Comandos:
  start       Iniciar containers (padrão)
  stop        Parar containers
  restart     Reiniciar containers
  status      Ver status dos containers
  logs        Ver logs (opcional: especificar serviço)
  test        Testar endpoints da API
  db          Ver dados do banco de dados
  shell       Acessar shell do container (opcional: especificar serviço)
  clean       Remover containers e volumes (⚠️ deleta dados!)

Exemplos:
  $0 start                 # Iniciar tudo
  $0 logs admcloud-api     # Ver logs da API
  $0 logs mysql            # Ver logs do MySQL
  $0 shell                 # Shell da API
  $0 shell mysql           # Shell do MySQL
  $0 test                  # Testar endpoints
  $0 db                    # Ver dados do banco

Documentação: DOCKER_SETUP.md
EOF
            exit 1
            ;;
    esac
}

main "$@"
