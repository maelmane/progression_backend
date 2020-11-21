#!/usr/bin/python3

import sys
import os
import yaml
from collections import defaultdict

import importer_mysql


def importer_répertoire(source, url):
    for thème in [d for d in os.scandir(source) if d.is_dir()]:
        importer_mysql.importer(importer_thème(thème.path, thème.name), url)


def importer_thème(path, nom_thème):
    with open(path + "/info.yml") as info_thème:
        thème = yaml.safe_load(info_thème)
        thème["nom"] = nom_thème
        thème["lang"] = thème["lang"] if "lang" in thème else None
        thème["séries"] = importer_séries(path, thème["séries"])
    return thème


def importer_séries(path, noms_séries):
    séries = []
    for n, s in enumerate(noms_séries):
        série = importer_série(path, s)
        série["numéro"] = n
        série["nom"] = s
        séries += [série]

    return séries


def importer_série(path, nom_série):
    path = path + "/" + nom_série
    with open(path + "/info.yml") as info_série:
        série = yaml.safe_load(info_série)
        série["questions"] = importer_questions(path, série["questions"])
    return série


def importer_questions(path, noms_questions):
    questions = []
    for n, q in enumerate(noms_questions):
        question = importer_question(path, q)
        question["numéro"] = n
        question["nom"] = q
        questions += [question]

    return questions


def importer_question(path, nom_question):
    path = path + "/" + nom_question
    with open(path + "/info.yml") as info_question:
        question = yaml.safe_load(info_question)
        question["feedback_pos"] = (
            question["feedback+"] if "feedback+" in question else None
        )
        question["feedback_neg"] = (
            question["feedback-"] if "feedback-" in question else None
        )
        question["exécutables"] = importer_exécutables(path, question["execs"])
        question["tests"] = importer_tests(path, question["tests"])
    return question


def importer_exécutables(path, noms_exécutables):
    exécutables = []
    for exécutable in noms_exécutables:
        fichier = exécutable["fichier"]
        exécutable["code"] = importer_exécutable(path + "/" + fichier)
        exécutables += [exécutable]
    return exécutables


def importer_exécutable(path):
    with open(path) as exécutable:
        return exécutable.read()


def importer_tests(path, noms_tests):
    tests = []
    n = 0
    for t in noms_tests:
        for test in importer_fichier_tests(path + "/" + t):
            test["numéro"] = n
            n += 1
            tests += [test]

    return tests


def importer_fichier_tests(path):
    with open(path) as info_test:
        tests = []
        tous_tests = yaml.safe_load_all(info_test)
        for test in tous_tests:
            test["feedback+"] = test["feedback+"] if "feedback+" in test else None
            test["feedback-"] = test["feedback-"] if "feedback-" in test else None
            test["params"] = test["params"] if "params" in test else None
            test["out"] = (
                str(test["out"])
                if str(test["out"])[-1] == "\n"
                else str(test["out"]) + "\n"
            )
            tests += [test]
    return tests


if len(sys.argv) < 3:
    print("Usage : importer.py répertoire_cible uri_db")
    exit(1)
source = sys.argv[1]
url = sys.argv[2]

importer_répertoire(source, url)
