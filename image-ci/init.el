;; publish.el --- Publish org-mode project on Gitlab Pages
;; Author: Rasmus

;;; Commentary:
;; This script will convert the org-mode files in this directory into
;; html.

;;; Code:

;; Bug fix : https://debbugs.gnu.org/cgi/bugreport.cgi?bug=34341
(setq gnutls-algorithm-priority "NORMAL:-VERS-TLS1.3")

;; CSS
(require 'htmlize)
(setq org-html-htmlize-output-type 'css)
;; CSS

(require 'package)
(package-initialize)

(require 'org)
(require 'ox-publish)
(require 'ob-napkin)
(org-babel-do-load-languages
 'org-babel-load-languages
 '((sql . t)
   (ditaa . t)
   (emacs-lisp . t)
   (python . t)
   (java . t)
   (shell . t)
   (napkin . t)
   (C . t)))

(setq org-babel-python-command "python3")

;; setting to nil, avoids "Author: x" at the bottom
(setq user-full-name nil)

(setq org-export-with-section-numbers nil
      org-export-with-smart-quotes t
      org-export-with-toc t)

(setq org-html-divs '((preamble "header" "top")
                      (content "main" "content")
                      (postamble "footer" "postamble"))
      org-html-container-element "section"
      org-html-metadata-timestamp-format "%Y-%m-%d"
      org-html-checkbox-type 'html
      org-html-html5-fancy t
      org-html-validation-link nil
      org-html-doctype "html5")
(setq
 org-use-sub-superscripts (quote {})
 org-export-with-sub-superscripts (quote {})
 org-export-default-language "fr"
 org-src-preserve-indentation t
 org-export-allow-bind-keywords t)

(setq org-confirm-babel-evaluate nil)

;; Config org
(defun org-video-export (path desc format)
  (cond
   ((eq format 'html)
	(format "<video alt=\"%s\" width=\"100%%\" controls><source src=\"%s\"></video>" desc path))
   ))
(defun org-img-export (path desc format)
  (cond
   ((eq format 'html)
	(format "<img src=\"%s\" alt=\"%s\"/>" path desc))
   ((eq format 'latex)
	(format "\\begin{center}\\includegraphics[width=.9\\linewidth]{%s}\\end{center}" path desc))))
(defun org-img-inline-export (path desc format &rest attr)
  (cond
   ((eq format 'html)
	(format "<img style=\"border:1px;\" src=\"data:image/%s;base64,%s\" alt=\"%s\" %s/>" (file-name-extension path) (file-to-base64 path) desc attr))
   ((eq format 'latex)
	(format "\\begin{center}\\includegraphics[width=.9\\linewidth]{%s}\\end{center}" path desc))))

(org-add-link-type "img" 'org-img-follow 'org-img-export)
(org-add-link-type "img-inline" 'org-img-follow 'org-img-inline-export)
(org-add-link-type "video" nil 'org-video-export)
