<?php
// Carrega a classe de conexao com o banco.
require_once __DIR__ . '/../config/conexao.php';
// Carrega o model Visita (a ficha de dados da visita).
require_once __DIR__ . '/../model/Visita.php';

// DAO = Data Access Object.
// Este arquivo concentra TODO o SQL de visitas.
// Regra simples: se tem SELECT/INSERT/UPDATE/DELETE de visitas, fica aqui.
class VisitaDAO
{
    // Conexao PDO reutilizada para as consultas.
    private PDO $conn;

    // Ao criar o DAO, abrimos/pegamos a conexao.
    public function __construct()
    {
        // getConn() devolve a conexao singleton.
        $this->conn = Conexao::getConn();
    }

    // Lista todas as visitas do banco.
    // Aqui usamos JOIN para trazer tambem o titulo do imovel.
    public function listar(): array
    {
        // SQL que junta visitas com imoveis.
        $sql = 'SELECT v.*, i.titulo AS imovel_titulo
                FROM visitas v
                JOIN imoveis i ON i.id = v.id_imovel
                ORDER BY v.id DESC';
        // Executa a consulta.
        $stmt = $this->conn->query($sql);
        // Array final de objetos Visita.
        $result = [];
        // Le cada linha retornada.
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Converte cada linha do banco para um objeto Visita.
            $result[] = $this->toModel($row);
        }
        // Devolve a lista pronta.
        return $result;
    }

    // Busca uma visita especifica pelo ID.
    // Retorna null se nao existir.
    public function buscarPorId(int $id): ?Visita
    {
        // Prepared statement para evitar SQL injection.
        $stmt = $this->conn->prepare('SELECT * FROM visitas WHERE id = ?');
        // Envia o ID para a consulta.
        $stmt->execute([$id]);
        // Pega uma unica linha.
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Se achou linha, converte para model. Se nao, retorna null.
        return $row ? $this->toModel($row) : null;
    }

    // Salva uma visita.
    // Se tiver ID: atualiza.
    // Se nao tiver ID: cria nova.
    public function salvar(Visita $visita): void
    {
        // getId() com valor significa registro existente.
        if ($visita->getId()) {
            // SQL de UPDATE.
            $stmt = $this->conn->prepare(
                'UPDATE visitas SET id_imovel = ?, nome = ?, email = ?, celular = ?, dia_semana = ?, periodo = ?, horario_preferencia = ? WHERE id = ?'
            );
            // Valores na mesma ordem dos ? do SQL.
            $stmt->execute([
                $visita->getIdImovel(),
                $visita->getNome(),
                $visita->getEmail(),
                $visita->getCelular(),
                $visita->getDiaSemana(),
                $visita->getPeriodo(),
                $visita->getHorarioPreferencia(),
                $visita->getId(),
            ]);
        } else {
            // SQL de INSERT para nova visita.
            $stmt = $this->conn->prepare(
                'INSERT INTO visitas (id_imovel, nome, email, celular, dia_semana, periodo, horario_preferencia) VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            // Valores na mesma ordem das colunas do INSERT.
            $stmt->execute([
                $visita->getIdImovel(),
                $visita->getNome(),
                $visita->getEmail(),
                $visita->getCelular(),
                $visita->getDiaSemana(),
                $visita->getPeriodo(),
                $visita->getHorarioPreferencia(),
            ]);
        }
    }

    // Verifica se ja existe visita no mesmo slot de agenda.
    // Regra de conflito: mesmo imovel + mesmo dia + mesmo horario.
    // Em edicao, ignoramos o proprio ID para nao acusar falso conflito.
    public function existeConflitoHorario(int $idImovel, string $diaSemana, string $horario, ?int $idIgnorar = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM visitas WHERE id_imovel = ? AND dia_semana = ? AND horario_preferencia = ?';
        $params = [$idImovel, $diaSemana, $horario];

        if ($idIgnorar !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $idIgnorar;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    // Exclui uma visita pelo ID.
    public function excluir(int $id): void
    {
        // Prepared statement por seguranca.
        $stmt = $this->conn->prepare('DELETE FROM visitas WHERE id = ?');
        // Executa com o ID recebido.
        $stmt->execute([$id]);
    }

    // Metodo auxiliar: transforma uma linha do banco em objeto Visita.
    // Isso evita repetir codigo em listar() e buscarPorId().
    private function toModel(array $row): Visita
    {
        // Cria um objeto vazio.
        $visita = new Visita();
        // Copia dados da linha para o objeto.
        $visita->setId((int) $row['id']);
        $visita->setIdImovel((int) $row['id_imovel']);
        $visita->setNome($row['nome']);
        $visita->setEmail($row['email']);
        $visita->setCelular($row['celular']);
        $visita->setDiaSemana($row['dia_semana']);
        $visita->setPeriodo($row['periodo']);
        $visita->setHorarioPreferencia((string) ($row['horario_preferencia'] ?? ''));
        // imovel_titulo so existe quando a consulta teve JOIN.
        if (isset($row['imovel_titulo'])) {
            $visita->setImovelTitulo($row['imovel_titulo']);
        }
        // Retorna o objeto montado.
        return $visita;
    }
}
