(require 'package)
(package-initialize)
(add-to-list 'package-archives '("org" . "https://orgmode.org/elpa/") t)
(add-to-list 'package-archives '("melpa" . "https://melpa.org/packages/") t)
(package-refresh-contents)
(package-install 'org-plus-contrib)
(setq org-confirm-babel-evaluate nil)

;; CSS
(require 'htmlize)
(setq org-html-htmlize-output-type 'css)
;; CSS

(require 'org)
(require 'ox-publish)
(org-babel-do-load-languages
 'org-babel-load-languages
 '((sql . t)
   (ditaa . t)
   (emacs-lisp . t)
   (python . t)
   (java . t)
   (shell . t)
   (C . t)))
