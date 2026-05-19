<?php
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../model/Contrato.php';

// Responsável por todas as operações de banco de dados relacionadas a contratos.
class ContratoDAO
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Conexao::getConn();
    }

    // Retorna todos os contratos com os nomes do imóvel, cliente e corretor incluídos.
    // Usa três JOINs pra trazer os nomes — sem isso teria só os IDs.
    public function listar(): array
    {
        $sql = 'SELECT c.*, i.titulo AS imovel_titulo, cl.nome AS cliente_nome, co.nome AS corretor_nome
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

    // Busca um contrato pelo ID; retorna null se não encontrar
    public function buscarPorId(int $id): ?Contrato
    {
        $stmt = $this->conn->prepare('SELECT * FROM contratos WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toModel($row) : null;
    }

    // Salva o contrato: se tiver ID atualiza, se não tiver cria novo
    public function salvar(Contrato $contrato): void
    {
        if ($contrato->getId()) {
            $stmt = $this->conn->prepare(
                'UPDATE contratos SET id_imovel = ?, id_cliente = ?, id_corretor = ?, tipo = ?, valor = ?, data_inicio = ?, data_fim = ? WHERE id = ?'
            );
            $stmt->execute([
                $contrato->getIdImovel(),
                $contrato->getIdCliente(),
                $contrato->getIdCorretor(),
                $contrato->getTipo(),
                $contrato->getValor(),
                $contrato->getDataInicio(),
                $contrato->getDataFim(),
                $contrato->getId(),
            ]);
        } else {
            $stmt = $this->conn->prepare(
                'INSERT INTO contratos (id_imovel, id_cliente, id_corretor, tipo, valor, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $contrato->getIdImovel(),
                $contrato->getIdCliente(),
                $contrato->getIdCorretor(),
                $contrato->getTipo(),
                $contrato->getValor(),
                $contrato->getDataInicio(),
                $contrato->getDataFim(),
            ]);
        }
    }

    // Remove o contrato do banco pelo ID
    public function excluir(int $id): void
    {
        $stmt = $this->conn->prepare('DELETE FROM contratos WHERE id = ?');
        $stmt->execute([$id]);
    }

    // Converte uma linha do banco em um objeto Contrato.
    // imovel_titulo, cliente_nome e corretor_nome só existem quando a query usou JOINs
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
        if (isset($row['imovel_titulo'])) {
            $contrato->setImovelTitulo($row['imovel_titulo']);
            $contrato->setClienteNome($row['cliente_nome']);
            $contrato->setCorretorNome($row['corretor_nome']);
        }
        return $contrato;
    }
}
