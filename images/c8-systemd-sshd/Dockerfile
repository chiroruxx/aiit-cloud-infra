FROM local/c8-systemd

EXPOSE 22

COPY copy_sshkey.sh /root/copy_sshkey.sh
RUN chmod +x /root/copy_sshkey.sh

RUN yum -y install initscripts \
    && yum check
RUN yum -y update \
    && yum -y install openssh-server

RUN sed -ri 's/^#PermitRootLogin yes/PermitRootLogin yes/' /etc/ssh/sshd_config \
    && sed -ri 's/^UsePAM yes/UsePAM no/' /etc/ssh/sshd_config \
    && sed -ri 's/^(root):[^:]*:(.*)$/\1:*:\2/' /etc/shadow

CMD ["bash", "-c", "/root/copy_sshkey.sh && exec /usr/sbin/init"]
