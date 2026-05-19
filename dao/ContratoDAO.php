<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Contrato.php';

class ContratoDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConn();
    }

    public function listar(): array
    {
        $sql = 'SELECT c.*, i.titulo AS imovel_titulo, i.planta_baixa AS imovel_planta_baixa, i.finalidade AS imovel_finalidade, cl.nome AS cliente_nome, co.nome AS corretor_nome
                FROM contratos c
                JOIN imoveis i     ON i.id  = c.id_imovel
                JOIN clientes cl   ON cl.id = c.id_cliente
                JOIN corretores co ON co.id = c.id_corretor
                ORDER BY c.id DESC';
        $stmt = $this->conn->query($sql);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $this->toModel($row);
        }
        return $result;
    }

    public function buscarPorId(int $id): ?Contrato
    {
        $stmt = $this->conn->prepare('SELECT * FROM contratos WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    public function salvar(Contrato $contrato): void
    {
        if ($contrato->getId()) {
            $stmt = $this->conn->prepare(
                'UPDATE contratos SET id_imovel = ?, id_cliente = ?, id_corretor = ?, tipo = ?, valor = ?, data_inicio = ?, data_fim = ?, status = ? WHERE id = ?'
            );
            $stmt->execute([
                $contrato->getIdImovel(),
                $contrato->getIdCliente(),
                $contrato->getIdCorretor(),
                $contrato->getTipo(),
                $contrato->getValor(),
                $contrato->getDataInicio(),
                $contrato->getDataFim(),
                $contrato->getStatus(),
                $contrato->getId(),
            ]);
        } else {
            $stmt = $this->conn->prepare(
                'INSERT INTO contratos (id_imovel, id_cliente, id_corretor, tipo, valor, data_inicio, data_fim, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $contrato->getIdImovel(),
                $contrato->getIdCliente(),
                $contrato->getIdCorretor(),
                $contrato->getTipo(),
                $contrato->getValor(),
                $contrato->getDataInicio(),
                $contrato->getDataFim(),
                $contrato->getStatus(),
            ]);
        }
    }

    public function encerrar(int $id): void
    {
        $stmt = $this->conn->prepare("UPDATE contratos SET status = 'encerrado' WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM contratos WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function toModel(array $row): Contrato
    {
        $contrato = new Contrato();
        $contrato->setId((int) $row['id']);
        $contrato->setIdImovel((int) $row['id_imovel']);
        $contrato->setIdCliente((int) $row['id_cliente']);
        $contrato->setIdCorretor((int) $row['id_corretor']);
        $contrato->setTipo($row['tipo']);
        $contrato->setValor((float) $row['valor']);
        $contrato->setDataInicio($row['data_inicio']);
        $contrato->setDataFim($row['data_fim'] ?? null);
        $contrato->setStatus($row['status'] ?? 'ativo');
        if (isset($row['imovel_titulo'])) {
            $contrato->setImovelTitulo($row['imovel_titulo']);
            $contrato->setImovelPlantaBaixa($row['imovel_planta_baixa'] ?? null);
            $contrato->setClienteNome($row['cliente_nome']);
            $contrato->setCorretorNome($row['corretor_nome']);
        }
        return $contrato;
    }
}
