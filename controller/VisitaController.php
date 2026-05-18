<?php
require_once __DIR__ . '/../dao/VisitaDAO.php';
require_once __DIR__ . '/../dao/ImovelDAO.php';

/**
 * Intermediário entre o formulário de agendamento e o banco de dados.
 *
 * Além das operações de CRUD, mantém o ImovelDAO pra popular o select
 * de imóveis no formulário — o visitante precisa escolher qual imóvel quer visitar.
 */
class VisitaController
{
    private VisitaDAO $dao;
    private ImovelDAO $imovelDAO;

    public function __construct()
    {
        $this->dao       = new VisitaDAO();
        $this->imovelDAO = new ImovelDAO();
    }

    /**
     * Retorna todas as visitas agendadas pra listagem.
     */
    public function listar(): array
    {
        return $this->dao->listar();
    }

    /**
     * Busca uma visita pelo ID pra pré-preencher o formulário de edição.
     */
    public function buscarPorId(int $id): ?Visita
    {
        return $this->dao->buscarPorId($id);
    }

    /**
     * Retorna todos os imóveis pra popular o select no formulário.
     */
    public function listarImoveis(): array
    {
        return $this->imovelDAO->listar();
    }

    /**
     * Recebe os dados do formulário, monta o objeto Visita e salva.
     */
    public function salvar(array $data): void
    {
        $visita = new Visita();
        $horario = trim($data['horario_preferencia'] ?? '');

        if (!empty($data['id'])) $visita->setId((int) $data['id']);
        $visita->setIdImovel((int) ($data['id_imovel'] ?? 0));
        $visita->setNome(trim($data['nome'] ?? ''));
        $visita->setEmail(trim($data['email'] ?? ''));
        $visita->setCelular(trim($data['celular'] ?? ''));
        $visita->setDiaSemana($data['dia_semana'] ?? 'segunda');
        $visita->setHorarioPreferencia($horario);
        $visita->setPeriodo($this->periodoPorHorario($horario));
        $this->dao->salvar($visita);
    }

    /**
     * Remove a visita com o ID informado do banco.
     */
    public function excluir(int $id): void
    {
        $this->dao->excluir($id);
    }

    private function periodoPorHorario(string $horario): string
    {
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $horario)) {
            return 'manha';
        }

        $hora = (int) substr($horario, 0, 2);

        if ($hora < 12) {
            return 'manha';
        }

        if ($hora < 18) {
            return 'tarde';
        }

        return 'noite';
    }
}
