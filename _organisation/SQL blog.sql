--------------------------------------

BDD : 
- projet_blog

TABLES : 
- membre
	- id_membre PK AI
	- pseudo (VARCHAR 255) UNIQUE
	- mdp (VARCHAR 255)
	- email (VARCHAR 255) UNIQUE
	- id_avatar (INT) FK
	- statut (INT)
	- nom (VARCHAR 255)
	- prenom (VARCHAR 255)
	- sexe (ENUM('m', 'f'))
	
- article
	- id_article PK AI
	- id_membre (INT) FK (auteur)
	- titre (VARCHAR 255)
	- date_enregistrement (DATETIME)
	- contenu (TEXT)
	- image_principale (VARCHAR 255)
	- etat (ENUM('public', 'brouillon', 'archive'))
	
- categorie
	- id_categorie PK AI
	- titre_categorie (VARCHAR 255)
	- description_categorie (TEXT)

- relation_article_categorie
	- id_relation_article_categorie PK AI
	- id_categorie FK
	- id_article FK
	
- mot_cle
	- id_mot_cle PK AI
	- titre_mot_cle (VARCHAR 255)
	- description_mot_cle (TEXT)
	
- relation_article_mot_cle
	- id_relation_article_mot_cle PK AI
	- id_mot_cle FK
	- id_article FK	
	
- avatar
	- id_avatar PK AI
	- avatar_src (VARCHAR 255)
	
- commentaire
	- id_commentaire PK AI
	- id_membre FK
	- id_article FK
	- message (TEXT)
	- date_commentaire (DATETIME)
	
	
	
	

