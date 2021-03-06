<?php include './layout/header.php'; ?>
<?php include './layout/menu.php'; ?>
<?php 
	$permissoes = retornaControle('perfil');
	
	if(empty($permissoes)) {
		header("Location: adminstrativa.php?msg=Acesso negado.");
	}
	require 'classes/Perfil.php'; 
	require 'classes/PerfilDAO.php';
	$perfil = new Perfil();
	if(isset($_GET['id']) && $_GET['id'] != '') {
		$id = $_GET['id'];
		$perfilDAO = new PerfilDAO();
		$perfil = $perfilDAO->get($id);
	}

?>

<div class="row" style="margin-top:40px">
	<div class="offset-3">
		<h2>Cadastrar perfil</h2>
	</div>
	<?php if($permissoes['insert']): ?>
	<div class="col-2">
		<a href="form_perfil.php" class="btn btn-success">Novo perfil</a>
	</div>
	<?php endif; ?>
</div>

<div class="row">
	<div class="col-6 <?= ( $perfil->getId() != '' ? '' : 'offset-3' )?>">
		<p>&nbsp;</p>
		<form action="controle_perfil.php?acao=<?= ( $perfil->getId() != '' ? 'editar' : 'cadastrar' )?>" method="post">
			<div class="form-group">
				<label for="id">ID</label>
				<input type="text" class="form-control" name="id" id="id" value="<?=($perfil->getId() != '' ? $perfil->getId() : '')?>" readonly>
			</div>
			<div class="form-group">
				<label for="nome">Descrição:</label>
				<input type="text" class="form-control" name="descricao" id="descricao" required value="<?= ($perfil->getDescricao() != '' ? $perfil->getDescricao() : '') ?>">
			</div>
			<div class="form-group">
				<label for="status">Status</label>
				<select name="status" class="form-control">
					<option value="1" <?= ($perfil->getStatus() == 1 ? 'selected' : '') ?>>Ativo</option>
					<option value="0" <?= ($perfil->getStatus() == 0 ? 'selected' : '') ?>>Inativo</option>
				</select>
			</div>
			<?php if(($permissoes['insert'] && $perfil->getId() == '') || ($permissoes['update'] && $perfil->getId() != '')): ?>
			<div class="form-group">
				<button type="submit" class="btn btn-primary">Salvar</button>
				<button type="reset" class="btn btn-warning">Resetar</button>
			</div>
			<?php endif; ?>
		</form>
	</div>
<?php if($perfil->getId() != ''): 
	require 'classes/Controle.php'; 
	require 'classes/ControleDAO.php';
	require 'classes/Permissao.php'; 
	require 'classes/PermissaoDAO.php';
	$permissoesPermissao = retornaControle('permissao');

	$controleDAO = new ControleDAO();
	$controles = $controleDAO->listar();
	$permissaoDAO = new PermissaoDAO();
	$permissoes = $permissaoDAO->listarControles("perfil_id = {$perfil->getId()}");
?>
	<div class="col-6">
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<?php if(!empty($permissoesPermissao)): ?>
		<div class="card">
			<div class="card-header">
				Cadastro de permissões
			</div>
			<div class="card-body">
				<form action="controle_perfil.php?acao=cadastraPermissao" method="post">
				<div class="form-group">
					<input type="hidden" name="perfil_id" value="<?= $perfil->getId(); ?>">
				    <select name="controle_id" class="form-control" required >
					    <option value="">Escolha o controle</option>
						<?php foreach($controles as $controle) : ?>
					    	<option value="<?= $controle->getId(); ?>"><?= $controle->getNome(); ?></option>
					    <?php endforeach; ?>
				    </select>
				</div>

				<div class="form-check form-check-inline">
				  <input class="form-check-input" checked type="checkbox" id="select" value="1" name="select">
				  <label class="form-check-label" for="select">select</label>
				</div>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" checked type="checkbox" id="insert" value="1" name="insert">
				  <label class="form-check-label" for="insert">insert</label>
				</div>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" checked type="checkbox" id="update" value="1" name="update">
				  <label class="form-check-label" for="update">update</label>
				</div>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" checked type="checkbox" id="delete" value="1" name="delete">
				  <label class="form-check-label" for="delete">delete</label>
				</div>
				<div class="form-check form-check-inline">
				  <input class="form-check-input" checked type="checkbox" id="show" value="1" name="show">
				  <label class="form-check-label" for="show">show</label>
				</div>
				<button type="submit" class="btn btn-primary w-100">Adicionar permissão</button>
				</form>
			</div>
		</div>
		<?php endif; ?>
		<div class="card">
				<div class="card-header">
					Permissões cadastradas
				</div>
				<div class="card-body">
					<table class="table">
						<tr>
							<th>Controle</th>
							<th>Permissões</th>
							<th>Ações</th>
						</tr>
						<?php foreach($permissoes as $permissao): ?>
						<tr>
							<td class="text-right">
								<strong><?= $permissao->controle; ?></strong>
							</td>
							<td>
								<?= ($permissao->getSelect() == 1 ? 
								'<span class="badge badge-primary">select</span>' 
								: ''); ?>
								<?= ($permissao->getInsert() == 1 ? 
								'<span class="badge badge-success">insert</span>'
								: '') ?>
								<?= ($permissao->getDelete() == 1 ? 
								'<span class="badge badge-danger">delete</span>'
								: '') ?> 
								<?= ($permissao->getUpdate() == 1 ? 
								'<span class="badge badge-warning">update</span>'
								: '') ?>
								<?= ($permissao->getShow() == 1 ? 
									'<span class="badge badge-info">show</span>'
									: ''
) ?>					</td>
							<td>
								<?php if(!empty($permissoesPermissao)): ?>
								<a href="controle_perfil.php?acao=deletaPermissao&id_permissao=<?= $permissao->getId(); ?>&id_perfil=<?= $perfil->getId(); ?>" class="btn btn-outline-danger" onclick="return confirm('Deseja realmente excluir?')">
									<i class="fas fa-trash"></i>
								</a>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
	</div>
<?php endif; ?>
</div>

<?php include './layout/footer.php'; ?>