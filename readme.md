# Dendreo

#### Logiciel de gestion pour centres de formation

## Installation

Dans le m�me r�pertoire que celui avec les ofs :

    mkdir dendreo && cd dendreo
    git init
    git remote add origin git@github.com:EtienneDepaulis/dendreo.git
    git remote add beta git@dendreo.com:dendreo.git
    git pull origin master

## Mise � jour

    git pull origin master

## Modifications (sur la branche master)

    git add --all
    git commit -m "Message de commit"
    git push origin master

puis pour mettre � jour b�ta :

    git push beta master

Un hook d'auto-pull est activ� sur le dossier /beta/

## Test d'une nouvelle fonctionnalit�

    git checkout -B nom_de_la_branche
    git add --all
    git commit -m "Message de commit"

Pour que la branche soit partag�e :

    git push origin nom_de_la_branche:nom_de_la_branche

Pour tester la branche en b�ta :

    git push beta nom_de_la_branche:master

Pour revenir � la branch master il faudra forcer le push car le comit de la nouvelle branch est plus r�cent que le dernier de la branche master :

    git push beta master:master -f
    
Pour merger une branche � master, il existes 2 options � savoir soit via GitHub avec les Pull Request, soit en manuel :

    git checkout master
    git pull origin master:master
    git checkout nom_de_la_branche
    git rebase master
    git checkout master
    git merge nom_de_la_branche

Pour expliquer le process tr�s simplement :

1. On se place dans la branche master
2. On met � jour master
3. On se place dans la nouvelle branche
4. On rebase (on replay les commits de master) - �tape primordial
5. On se replace dans la branche master
6. On merge la nouvelle branche

Pour supprimer une branche sur origin :

    git push origin :nom_de_la_branche
    
Pour la supprimer en local :

    git branch -d nom_de_la_branche
    
Pour travailler sur une branche qui a �t� push�e sur origin :

    git checkout -b nom_de_la_branche origin/nom_de_la_branche 

## Fonctions pratiques

Lister toutes les branches :

    git branch -r

Savoir quelles branches sont track�es ou pas :

    git remote show origin

## Propager une modif sur d'autres branches

    git checkout master
    git commit -m "Modif critique"
    git checkout une_branche_au_hasard
    git rebase master

L'id�e est qu'on r�alise le commit sur la branche master, puis qu'on rebase sur les autres branches (rebase = replay des commits depuis la s�paration de branches). Il n'est pas n�cessaire de pousser le rebase, on n'est que deux ;)
