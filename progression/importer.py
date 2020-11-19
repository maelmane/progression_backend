#!python3

import sys
import os
import yaml
from collections import defaultdict

import importer_mysql


def importer_répertoire(source, url):
    for thème in [d for d in os.scandir(source) if d.is_dir()]:
        importer_mysql.importer(importer_thème(thème.path), url)


def importer_thème(path):
    with open(path + "/info.yml") as info_thème:
        thème = yaml.safe_load(info_thème)
        # titre = thème["titre"]
        # desc = thème["description"]
        thème["lang"] = thème["lang"] if "lang" in thème else None
        thème["séries"] = importer_séries(path, thème["séries"])
    return thème
    # ajouter_thème(titre, description, séries)


def importer_séries(path, titre_séries):
    séries = []
    for n, s in enumerate(titre_séries):
        série = importer_série(path, s)
        série["numéro"] = n
        séries += [série]

    return séries


def importer_série(path, série):
    path = path + "/" + série
    with open(path + "/info.yml") as info_série:
        série = yaml.safe_load(info_série)
        # titre = série["titre"]
        # desc = série["description"]
        série["questions"] = importer_questions(path, série["questions"])
    return série


def importer_questions(path, questions):
    return [importer_question(path, s) for s in questions]


def importer_question(path, question):
    path = path + "/" + question
    with open(path + "/info.yml") as info_question:
        question = yaml.safe_load(info_question)
        # titre = question["titre"]
        # desc = question["description"]
        # énoncé = question["énoncé"]
        question["feedback_pos"] = (
            question["feedback+"] if "feedback+" in question else None
        )
        question["feedback_neg"] = (
            question["feedback-"] if "feedback-" in question else None
        )
        question["exécutables"] = importer_exécutables(path, question["execs"])
        question["tests"] = importer_tests(path, question["tests"])
    return question


def importer_exécutables(path, exécutables):
    for exécutable in exécutables:
        # langage = exécutable["langage"]
        fichier = exécutable["fichier"]
        exécutable["code"] = importer_exécutable(path + "/" + fichier)
    return exécutables


def importer_exécutable(path):
    with open(path) as exécutable:
        return exécutable


def importer_tests(path, tests):
    return [importer_test(path + "/" + s) for s in tests]


def importer_test(path):
    with open(path) as info_test:
        tests = yaml.safe_load_all(info_test)
        for test in tests:
            # nom = test["nom"]
            # entrée = test["in"]
            # sortie = test["out"]
            test["feedback+"] = test["feedback+"] if "feedback+" in test else None
            test["feedback-"] = test["feedback-"] if "feedback-" in test else None
    return tests


if len(sys.argv) < 3:
    print("Usage : importer.py répertoire_cible uri_db")
    exit(1)
source = sys.argv[1]
url = sys.argv[2]

importer_répertoire(source, url)
