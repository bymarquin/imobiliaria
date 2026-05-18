<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Visita.php';

/**
 * Cuida de tudo que envolve visitas agendadas no banco de dados.
 *
 * A listagem usa um JOIN com imoveis pra já trazer o título do imóvel junto,
 * evitando uma segunda consulta só pra mostrar esse dado na tela.
 */
class VisitaDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConn();
    }

    /**
     * Retorna todas as visitas agendadas com o título do imóvel já incluído.
     */
    public function listar(): array
    {
        $sql = 'SELECT v.*, i.titulo AS imovel_titulo
                FROM visitas v
                JOIN imoveis i ON i.id = v.id_imovel
                ORDER BY v.id DESC';
        $stmt = $this->conn->query($sql);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $this->toModel($row);
        }
        return $result;
    }

    /**
     * Busca uma visita pelo ID. Retorna null se não achar.
     */
    public function buscarPorId(int $id): ?Visita
    {
        $stmt = $this->conn->prepare('SELECT * FROM visitas WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    /**
     * Salva uma visita no banco. Se tem ID, atualiza; se não tem, cria nova.
     */
    public function salvar(Visita $visita): void
    {
        if ($visita->getId()) {
            $stmt = $this->conn->prepare(
                'UPDATE visitas SET id_imovel = ?, nome = ?, email = ?, celular = ?, dia_semana = ?, periodo = ?, horario_preferencia = ? WHERE id = ?'
            );
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
            $stmt = $this->conn->prepare(
                'INSERT INTO visitas (id_imovel, nome, email, celular, dia_semana, periodo, horario_preferencia) VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
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

    /**
     * Remove uma visita pelo ID.
     */
    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM visitas WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * Converte uma linha do banco em um objeto Visita.
     */
    private function toModel(array $row): Visita
    {
        $visita = new Visita();
        $visita->setId((int) $row['id']);
        $visita->setIdImovel((int) $row['id_imovel']);
        $visita->setNome($row['nome']);
        $visita->setEmail($row['email']);
        $visita->setCelular($row['celular']);
        $visita->setDiaSemana($row['dia_semana']);
        $visita->setPeriodo($row['periodo']);
        $visita->setHorarioPreferencia((string) ($row['horario_preferencia'] ?? ''));
        if (isset($row['imovel_titulo'])) {
            $visita->setImovelTitulo($row['imovel_titulo']);
        }
        return $visita;
    }
}
