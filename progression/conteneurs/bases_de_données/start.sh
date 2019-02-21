iptables -F                                                                                                                                                                            
iptables -t nat -F                                                                                                                                                                     
iptables -X                                                                                                                                                                            
iptables -t nat -A PREROUTING -d $(hostname -i) -p tcp --dport 3306 -j DNAT --to 172.17.0.2                                                                                                
iptables -t nat -A POSTROUTING -d 172.17.0.2 -j SNAT --to $(hostname -i)

sleep 600;

