Delimiter $$

Drop Procedure if Exists listarUsuarios$$
Create Procedure listarUsuarios()
begin
	Select u.nm_email, u.nm_usuario, u.cd_tipo_usuario, tu.nm_tipo_usuario
	from usuario u join tipo_usuario tu on (u.cd_tipo_usuario=tu.cd_tipo_usuario)
	order by nm_usuario;
end$$


Drop Procedure if Exists listarProfessores$$
Create Procedure listarProfessores()
begin
	Select u.nm_email, u.nm_usuario, u.cd_tipo_usuario, tu.nm_tipo_usuario
	from usuario u join tipo_usuario tu on (u.cd_tipo_usuario=tu.cd_tipo_usuario)
    where u.cd_tipo_usuario = 2
	order by nm_usuario;
end$$


Drop Procedure if Exists listarAlunos$$
Create Procedure listarAlunos()
begin
	Select u.nm_email, u.nm_usuario, u.cd_tipo_usuario, tu.nm_tipo_usuario
	from usuario u join tipo_usuario tu on (u.cd_tipo_usuario=tu.cd_tipo_usuario)
    where u.cd_tipo_usuario = 3
	order by nm_usuario;
end$$

Drop Procedure if Exists criarUsuario$$
Create Procedure criarUsuario(
	pEmail varchar(200), 
    pNome varchar(200), 
    pSenha varchar(15), 
    pTipo int)
begin
	declare qtd int default 0;
        
	Select count(*) into qtd from usuario where nm_email = pEmail;
    if (qtd = 0) Then
		Insert into usuario values (pEmail, pNome, md5(pSenha), pTipo);
    else
		signal sqlstate '45000' set message_text = 'E-mail já cadastrado';
	end if;
end$$

Drop Procedure if Exists atualizaDadosUsuario$$
Create Procedure atualizaDadosUsuario(pEmail varchar(200), pNome varchar(200), pSenha varchar(15))
begin
	declare qtd int default 0;
    
    Select count(*) into qtd from usuario where nm_email=pEmail;
    if (qtd = 0) Then
		signal sqlstate '45000' set message_text = 'Usuário não encontrado!';
    else
		Update usuario set nm_usuario = pNome, nm_senha = md5(pSenha)
			where nm_email=pEmail;
    end if;
end$$

Drop Procedure if exists excluirUsuario$$
Create Procedure excluirUsuario(pEmail varchar(200))
begin
	declare qtd int default 0;
    
    Select count(*) into qtd from usuario where nm_email=pEmail;
    if (qtd = 0) Then
		signal sqlstate '45000' set message_text = 'Usuário não encontrado!';
    else
		Delete from usuario where nm_email = pEmail;
    end if;
end$$

Drop Procedure if exists verificarAcesso$$
Create Procedure verificarAcesso(pLogin varchar(200), pSenha varchar(64))
begin
	Declare qtd int default 0;
    
    Select count(*) into qtd from usuario 
		where nm_email = pLogin and nm_senha = md5(pSenha);
        
	if (qtd = 0) then
		signal sqlstate '45000' set message_text = 'Login e/ou Senha inválidos';
    else
		Select u.nm_email, u.nm_usuario, u.cd_tipo_usuario, tu.nm_tipo_usuario
			from usuario u join tipo_usuario tu on (u.cd_tipo_usuario = tu.cd_tipo_usuario)
				where u.nm_email = pLogin and u.nm_senha = md5(pSenha);
    end if;
end$$

Delimiter ;