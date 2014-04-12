# Dendreo

#### Logiciel de gestion pour centres de formation

## Installation

Dans le même répertoire que celui avec les ofs :

    mkdir dendreo && cd dendreo
    git init
    git remote add origin git@github.com:EtienneDepaulis/dendreo.git
    git remote add beta git@dendreo.com:dendreo.git
    git pull origin master

## Mise à jour

    git pull origin master

## Modifications (sur la branche master)

    git add --all
    git commit -m "Message de commit"
    git push origin master

puis pour mettre à jour béta :

    git push beta master

Un hook d'auto-pull est activé sur le dossier /beta/

## Test d'une nouvelle fonctionnalité

    git checkout -B nom_de_la_branche
    git add --all
    git commit -m "Message de commit"

Pour que la branche soit partagée :

    git push origin nom_de_la_branche:nom_de_la_branche

Pour tester la branche en béta :

    git push beta nom_de_la_branche:master

Pour revenir à la branch master il faudra forcer le push car le comit de la nouvelle branch est plus récent que le dernier de la branche master :

    git push beta master:master -f
    
Pour merger une branche à master, il existes 2 options à savoir soit via GitHub avec les Pull Request, soit en manuel :

    git checkout master
    git pull origin master:master
    git checkout nom_de_la_branche
    git rebase master
    git checkout master
    git merge nom_de_la_branche

Pour expliquer le process très simplement :

1. On se place dans la branche master
2. On met à jour master
3. On se place dans la nouvelle branche
4. On rebase (on replay les commits de master) - étape primordial
5. On se replace dans la branche master
6. On merge la nouvelle branche

Pour supprimer une branche sur origin :

    git push origin :nom_de_la_branche
    
Pour la supprimer en local :

    git branch -d nom_de_la_branche
    
Pour travailler sur une branche qui a été pushée sur origin :

    git checkout -b nom_de_la_branche origin/nom_de_la_branche 

## Fonctions pratiques

Lister toutes les branches :

    git branch -r

Savoir quelles branches sont trackées ou pas :

    git remote show origin

## Propager une modif sur d'autres branches

    git checkout master
    git commit -m "Modif critique"
    git checkout une_branche_au_hasard
    git rebase master

L'idée est qu'on réalise le commit sur la branche master, puis qu'on rebase sur les autres branches (rebase = replay des commits depuis la séparation de branches). Il n'est pas nécessaire de pousser le rebase, on n'est que deux ;)