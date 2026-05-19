<?php
require_once __DIR__ . '/../dao/VisitaDAO.php';
require_once __DIR__ . '/../dao/ImovelDAO.php';

class VisitaController
{
    private VisitaDAO $dao;
    private ImovelDAO $imovelDAO;

    public function __construct()
    {
        $this->dao       = new VisitaDAO();
        $this->imovelDAO = new ImovelDAO();
    }

    public function listar(): array
    {
        return $this->dao->listar();
    }

    public function buscarPorId(int $id): ?Visita
    {
        return $this->dao->buscarPorId($id);
    }

    public function listarImoveis(): array
    {
        return $this->imovelDAO->listar();
    }

    public function salvar(array $data): void
    {
        $visita  = new Visita();
        $horario = trim($data['horario_preferencia'] ?? '');

        if (!empty($data['id'])) $visita->setId((int) $data['id']);

        $visita->setIdImovel((int) ($data['id_imovel'] ?? 0));
        $visita->setNome(trim($data['nome'] ?? ''));
        $visita->setEmail(trim($data['email'] ?? ''));
        $visita->setCelular(trim($data['celular'] ?? ''));
        $visita->setDiaSemana($data['dia_semana'] ?? 'segunda');
        $visita->setHorarioPreferencia($horario);

        $idAtual = $visita->getId();
        $conflito = $this->dao->existeConflitoHorario(
            $visita->getIdImovel(),
            $visita->getDiaSemana(),
            $visita->getHorarioPreferencia(),
            $idAtual
        );

        if ($conflito) {
            throw new RuntimeException('Este horario ja esta agendado para o imovel selecionado. Escolha outro horario.');
        }

        $visita->setPeriodo($this->periodoPorHorario($horario));

        $this->dao->salvar($visita);
    }

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

        if ($hora < 12) return 'manha';
        if ($hora < 18) return 'tarde';
        return 'noite';
    }
}
